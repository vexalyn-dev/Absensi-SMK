<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use App\Models\Classroom;
use App\Models\ScanLog;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassAttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;

        $schedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject', 'classAttendances' => function($q) use ($user, $today) {
                $q->where('user_id', $user->id)
                  ->whereDate('date', $today);
            }])
            ->orderBy('start_time')
            ->get();

        $totalClasses      = $schedules->count();
        $completedClasses  = $schedules->filter(fn($s) => $s->classAttendances->first()?->isComplete())->count();
        $inProgressClasses = $schedules->filter(fn($s) => $s->classAttendances->first()?->check_in_time && !$s->classAttendances->first()?->check_out_time)->count();

        return view('teacher.class-attendance.index', compact(
            'schedules', 'totalClasses', 'completedClasses', 'inProgressClasses'
        ));
    }

    /**
     * Helper untuk mencatat audit trail setiap scan.
     */
    private function logScan($user, $classroom, $mode, $status, $message, $request)
    {
        ScanLog::create([
            'user_id'      => $user->id,
            'classroom_id' => $classroom?->id,
            'mode'         => $mode,
            'status'       => $status,
            'message'      => $message,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'latitude'     => $request->input('latitude'),
            'longitude'    => $request->input('longitude'),
            'device_info'  => $request->header('User-Agent'),
            'metadata'     => [
                'qr_data'         => $request->input('qr_data'),
                'schedule_period' => $request->input('period'),
                'schedule_id'     => $request->input('schedule_id')
            ],
            'scanned_at'   => now(),
        ]);
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'mode'    => 'required|in:in,out',
            'schedule_id' => 'nullable|integer'
        ]);

        $user      = auth()->user();
        $now       = Carbon::now();
        $today     = $now->toDateString();
        $dayOfWeek = $now->dayOfWeek;
        $mode      = $request->mode;

        // Decode QR data (format: classroom_id|random_token atau JSON)
        $qrData = $request->qr_data;
        $classroomId = null;

        // Coba parse JSON (jika QR menggunakan format JSON seperti di ClassAttendanceController)
        $parsedJson = json_decode($qrData, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($parsedJson['classroom_id'])) {
            $classroomId = $parsedJson['classroom_id'];
        } else {
            $qrParts = explode('|', $qrData);
            if (count($qrParts) < 1) {
                $this->logScan($user, null, $mode, 'failed', 'QR Code tidak valid', $request);
                return response()->json(['success' => false, 'message' => 'QR Code tidak valid'], 400);
            }
            $classroomId = $qrParts[0];
        }

        // 1. Cari kelas dari QR
        $classroom = Classroom::find($classroomId);
        if (!$classroom) {
            $this->logScan($user, null, $mode, 'failed', 'Kelas tidak ditemukan (ID: ' . $classroomId . ')', $request);
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        // 2. Cek apakah ini shared space (aula/gor/mushola)
        $isSharedSpace = (bool) $classroom->is_shared || $classroom->type === 'shared';

        if ($isSharedSpace && !$request->filled('selected_classroom_id')) {
            $allClasses = Classroom::where('is_active', true)
                ->where(function ($q) {
                    $q->where('is_shared', false)
                      ->orWhereNull('is_shared');
                })
                ->where(function ($q) {
                    $q->where('type', 'regular')
                      ->orWhereNull('type');
                })
                ->orderBy('name')
                ->get();

            $subjectIds = TeachingSchedule::where('user_id', $user->id)
                ->where('is_active', true)
                ->whereNotNull('subject_id')
                ->distinct()
                ->pluck('subject_id');

            $subjects = Subject::whereIn('id', $subjectIds)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'is_shared_space' => true,
                'classroom' => [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'code' => $classroom->code,
                ],
                'all_classes' => $allClasses->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->code,
                ])->values(),
                'subjects' => $subjects->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                ])->values(),
                'message' => $mode === 'in' ? 'Pilih kelas yang sedang diajar' : 'Pilih data presensi keluar',
            ]);
        }

        if ($isSharedSpace && $mode === 'out') {
            $request->validate([
                'selected_classroom_id' => 'required|exists:classrooms,id',
                'subject_id' => 'required|exists:subjects,id',
                'period' => 'required|integer|min:1|max:12',
            ]);

            $selectedClassroom = Classroom::find($request->selected_classroom_id);
            $attendance = ClassAttendance::where('user_id', $user->id)
                ->where('classroom_id', $classroom->id)
                ->where('selected_classroom_id', $request->selected_classroom_id)
                ->where('subject_id', $request->subject_id)
                ->where('period', $request->period)
                ->whereDate('date', $today)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (!$attendance) {
                return response()->json(['success' => false, 'message' => 'Tidak ada presensi masuk yang ditemukan untuk data ini!'], 400);
            }

            $duration = $now->diffInMinutes($attendance->check_in_time);
            if ($duration < 30) {
                return response()->json(['success' => false, 'message' => "Durasi mengajar terlalu singkat! Minimal 30 menit (baru {$duration} menit)"], 400);
            }

            $attendance->check_out_time = $now;
            $attendance->save();

            $this->logScan($user, $classroom, 'out', 'success', "Scan keluar di {$classroom->name} untuk kelas {$selectedClassroom->name}", $request);

            return response()->json([
                'success' => true,
                'message' => 'Scan keluar berhasil!',
                'data' => [
                    'location' => $classroom->name,
                    'classroom' => $selectedClassroom->name,
                    'duration' => $duration . ' menit',
                    'check_in' => $attendance->check_in_time->format('H:i'),
                    'check_out' => $now->format('H:i'),
                ],
            ]);
        }

        // 3. Cari jadwal guru hari ini
        $schedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->when(!$isSharedSpace, function($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId); // Jika BUKAN shared space, filter berdasarkan lokasi spesifik
            })
            ->with(['classroom', 'subject'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->logScan($user, $classroom, $mode, 'failed', 'Tidak ada jadwal mengajar di lokasi ini hari ini', $request);
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal mengajar hari ini'
            ], 400);
        }

        // Jika guru mensubmit pilihan kelas spesifik
        if ($request->has('schedule_id')) {
            $activeSchedule = $schedules->where('id', $request->schedule_id)->first();
            if ($activeSchedule) {
                $res = $this->processAttendanceForSchedule($activeSchedule, $user, $now, $mode, $classroom, $request);
                return response()->json($res, $res['success'] ? 200 : 400);
            }
            return response()->json([
                'success' => false,
                'message' => 'Jadwal yang dipilih tidak valid.'
            ], 400);
        }

        // 4. Handle jika shared space dengan multiple jadwal ATAU reguler space tapi multiple jadwal
        if ($schedules->count() > 1) {
            return response()->json([
                'success' => true,
                'message' => $classroom->name,
                'schedules' => $schedules->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'classroom_name' => $schedule->classroom->name ?? '-',
                        'subject' => $schedule->subject->name ?? '-',
                        'period' => $schedule->period,
                        'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                        'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
                    ];
                })->values()
            ]);
        }

        // 5. Cari jadwal yang AKTIF berdasarkan waktu sekarang (untuk single schedule auto-match)
        $activeSchedule = $schedules->first(function($schedule) use ($now) {
            $start           = Carbon::parse($schedule->start_time)->subMinutes(15);
            $end             = Carbon::parse($schedule->end_time)->addMinutes(15);
            return $now->between($start, $end);
        });

        if (!$activeSchedule) {
            $nearestSchedule = $schedules->sortBy(function($s) use ($now) {
                return abs(Carbon::parse($s->start_time)->diffInMinutes($now));
            })->first();

            $message = $now->lt(Carbon::parse($nearestSchedule->start_time))
                ? "Terlalu cepat! Jadwal mulai pukul " . Carbon::parse($nearestSchedule->start_time)->format('H:i')
                : "Waktu scan sudah lewat. Jadwal berakhir pukul " . Carbon::parse($nearestSchedule->end_time)->format('H:i');

            $this->logScan($user, $classroom, $mode, 'failed', $message, $request);
            return response()->json(['success' => false, 'message' => $message], 400);
        }

        $res = $this->processAttendanceForSchedule($activeSchedule, $user, $now, $mode, $classroom, $request);
        return response()->json($res, $res['success'] ? 200 : 400);
    }

    public function saveSharedSpaceAttendance(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'selected_classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'period' => 'required|integer|min:1|max:12',
            'check_in_time' => 'required|date',
        ]);

        $user = auth()->user();
        $now = Carbon::now();
        $today = $now->toDateString();

        $location = Classroom::find($request->classroom_id);
        $selectedClassroom = Classroom::find($request->selected_classroom_id);
        $subject = Subject::find($request->subject_id);

        if (!$location || (! $location->is_shared && $location->type !== 'shared')) {
            return response()->json(['success' => false, 'message' => 'Ruangan bersama tidak valid'], 400);
        }

        $existing = ClassAttendance::where('user_id', $user->id)
            ->where('classroom_id', $request->classroom_id)
            ->where('selected_classroom_id', $request->selected_classroom_id)
            ->where('subject_id', $request->subject_id)
            ->where('period', $request->period)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Presensi untuk sesi ini sudah ada!'], 400);
        }

        $attendance = ClassAttendance::create([
            'user_id' => $user->id,
            'classroom_id' => $request->classroom_id,
            'selected_classroom_id' => $request->selected_classroom_id,
            'subject_id' => $request->subject_id,
            'period' => $request->period,
            'date' => $today,
            'check_in_time' => Carbon::parse($request->check_in_time),
            'status' => 'Hadir',
            'scan_method' => 'qr_shared_space',
            'notes' => "Mengajar {$selectedClassroom->name} - {$subject->name} di {$location->name}",
        ]);

        $this->logScan($user, $location, 'in', 'success', "Scan masuk di {$location->name} untuk kelas {$selectedClassroom->name}", $request);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan!',
            'data' => [
                'location' => $location->name,
                'classroom' => $selectedClassroom->name,
                'subject' => $subject->name,
                'period' => $request->period,
                'check_in_time' => $attendance->check_in_time->format('H:i'),
            ],
        ]);
    }

    private function processAttendanceForSchedule($activeSchedule, $user, $now, $mode, $scannedClassroom, $request)
    {
        $today = $now->toDateString();

        $attendance = ClassAttendance::firstOrNew([
            'user_id'      => $user->id,
            'classroom_id' => $activeSchedule->classroom_id,
            'teaching_schedule_id' => $activeSchedule->id,
            'period'       => $activeSchedule->period,
            'date'         => $today,
        ]);

        $scheduleClassroomName = $activeSchedule->classroom->name ?? '-';

        if ($mode === 'in') {
            if ($attendance->exists && $attendance->check_in_time) {
                $this->logScan($user, $scannedClassroom, 'in', 'duplicate', 'Sudah scan masuk di kelas ' . $scheduleClassroomName, $request);
                return [
                    'success' => false,
                    'message' => 'Anda sudah scan masuk di kelas ' . $scheduleClassroomName . '!'
                ];
            }

            $scheduleStart = Carbon::parse($activeSchedule->start_time);
            $isLate        = $now->gt($scheduleStart);

            $attendance->check_in_time = $now;
            $attendance->status        = $isLate ? 'Terlambat' : 'Hadir';
            $attendance->scan_method   = 'qr_in';
            $attendance->save();

            $this->logScan($user, $scannedClassroom, 'in', 'success', 'Scan masuk berhasil di kelas ' . $scheduleClassroomName . ' (' . $attendance->status . ') via ' . $scannedClassroom->name, $request);

            return [
                'success' => true,
                'message' => '✅ Scan masuk berhasil di kelas ' . $scheduleClassroomName,
                'data'    => [
                    'classroom'     => $scheduleClassroomName,
                    'subject'       => $activeSchedule->subject->name ?? '-',
                    'period'        => $activeSchedule->period,
                    'check_in_time' => $now->format('H:i'),
                    'status'        => $attendance->status,
                    'message'       => $isLate
                        ? 'Terlambat ' . $now->diffInMinutes($scheduleStart) . ' menit'
                        : 'Tepat waktu',
                ]
            ];
        }

        if ($mode === 'out') {
            if (!$attendance->exists || !$attendance->check_in_time) {
                $this->logScan($user, $scannedClassroom, 'out', 'failed', 'Belum scan masuk di kelas ' . $scheduleClassroomName, $request);
                return [
                    'success' => false,
                    'message' => 'Anda belum scan masuk di kelas ' . $scheduleClassroomName . '!'
                ];
            }

            if ($attendance->check_out_time) {
                $this->logScan($user, $scannedClassroom, 'out', 'duplicate', 'Sudah scan keluar di kelas ' . $scheduleClassroomName, $request);
                return [
                    'success' => false,
                    'message' => 'Anda sudah scan keluar di kelas ' . $scheduleClassroomName . '!'
                ];
            }

            $duration = $now->diffInMinutes($attendance->check_in_time);
            if ($duration < 30) {
                $this->logScan($user, $scannedClassroom, 'out', 'failed', "Durasi terlalu singkat ({$duration} menit) di kelas " . $scheduleClassroomName, $request);
                return [
                    'success' => false,
                    'message' => "Durasi mengajar terlalu singkat untuk kelas {$scheduleClassroomName}! Minimal 30 menit (baru {$duration} menit)"
                ];
            }

            $attendance->check_out_time = $now;

            if ($duration < 45 && $attendance->status === 'Hadir') {
                $attendance->status = 'Terlambat';
            }

            $attendance->save();

            $this->logScan($user, $scannedClassroom, 'out', 'success', "Scan keluar berhasil di kelas {$scheduleClassroomName}, durasi {$duration} menit via {$scannedClassroom->name}", $request);

            \App\Helpers\NotificationHelper::send(
                $user,
                'success',
                'Presensi Kelas Selesai',
                "Anda telah menyelesaikan mengajar di kelas {$scheduleClassroomName} selama {$duration} menit",
                route('teacher.class-attendance'),
                'check-circle',
                'bg-green-100 text-green-600'
            );

            return [
                'success' => true,
                'message' => '✅ Scan keluar berhasil untuk kelas ' . $scheduleClassroomName . '!',
                'data'    => [
                    'classroom' => $scheduleClassroomName,
                    'subject'   => $activeSchedule->subject->name ?? '-',
                    'duration'  => $duration . ' menit',
                    'check_in'  => $attendance->check_in_time->format('H:i'),
                    'check_out' => $now->format('H:i'),
                    'status'    => $attendance->status,
                ]
            ];
        }

        return ['success' => false, 'message' => 'Mode tidak valid'];
    }
}
