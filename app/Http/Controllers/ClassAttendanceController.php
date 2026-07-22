<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClassAttendanceController extends Controller
{
    /**
     * Halaman scan QR kelas
     */
    public function scan()
    {
        $todaySchedules = TeachingSchedule::getTodaySchedules(auth()->id());
        $todayAttendances = ClassAttendance::where('user_id', auth()->id())
            ->where('date', today())
            ->with('classroom')
            ->get()
            ->keyBy(function ($att) {
                return $att->classroom_id . '_' . $att->period;
            });

        return view('class-attendance.scan', compact('todaySchedules', 'todayAttendances'));
    }

    /**
     * Proses scan QR kelas.
     * Support format:
     *   1. JSON baru : {"type":"classroom","classroom_id":1,"token":"uuid"}
     *   2. JSON lama : {"classroom_id":1,"token":"uuid"}   (tanpa type)
     *   3. Pipe lama : "1|X-RPL"                           (legacy, cocokkan by id saja)
     *
     * Mendukung response JSON (AJAX) maupun redirect (form biasa).
     */
    public function store(Request $request)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);

        $raw       = trim($validated['qr_data']);
        $classroom = null;

        // ── Coba parse sebagai JSON ──────────────────────────────────────────
        $decoded = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Wajib punya classroom_id
            if (empty($decoded['classroom_id'])) {
                return $this->respond($isAjax, false, 'QR Code tidak valid — classroom_id tidak ditemukan.', 422);
            }

            // Jika ada field "type", harus bernilai "classroom"
            if (isset($decoded['type']) && $decoded['type'] !== 'classroom') {
                return $this->respond($isAjax, false, 'QR Code ini bukan QR kelas (type salah).', 422);
            }

            // Validasi token jika tersedia
            if (!empty($decoded['token'])) {
                $classroom = Classroom::where('id', $decoded['classroom_id'])
                    ->where('qr_token', $decoded['token'])
                    ->first();

                if (!$classroom) {
                    return $this->respond($isAjax, false, 'QR Code tidak valid atau token kelas sudah kedaluwarsa. Minta admin regenerate QR.', 422);
                }
            } else {
                // Token tidak ada — cocokkan by id saja (format transisi)
                $classroom = Classroom::find($decoded['classroom_id']);
                if (!$classroom) {
                    return $this->respond($isAjax, false, 'Kelas tidak ditemukan.', 404);
                }
            }

        } else {
            // ── Fallback: format pipe lama "id|code" ────────────────────────
            $parts       = explode('|', $raw);
            $classroomId = $parts[0] ?? null;

            if (!$classroomId || !is_numeric($classroomId)) {
                Log::warning('ClassAttendance scan: format QR tidak dikenali', ['raw' => $raw]);
                return $this->respond($isAjax, false, 'Format QR Code tidak dikenali. Pastikan Anda scan QR yang benar.', 422);
            }

            $classroom = Classroom::find((int) $classroomId);
            if (!$classroom) {
                return $this->respond($isAjax, false, 'Kelas tidak ditemukan dari QR Code ini.', 404);
            }
        }

        // ── Pastikan kelas aktif ─────────────────────────────────────────────
        if (!$classroom->is_active) {
            return $this->respond($isAjax, false, "Kelas {$classroom->name} sudah tidak aktif.", 422);
        }

        $now         = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $today       = today();

        // ── Cari jadwal yang cocok ───────────────────────────────────────────
        $schedule = TeachingSchedule::findMatchingSchedule(auth()->id(), $classroom->id);

        if (!$schedule) {
            // Fallback: cari jadwal hari ini tanpa filter waktu (agar tetap bisa presensi)
            $dayOfWeek = $now->dayOfWeek;
            $schedule  = TeachingSchedule::where('user_id', auth()->id())
                ->where('classroom_id', $classroom->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->orderByRaw('ABS(TIMESTAMPDIFF(MINUTE, start_time, ?)) ASC', [$now->format('H:i:s')])
                ->first();
        }

        if (!$schedule) {
            return $this->respond($isAjax, false, "Anda tidak memiliki jadwal mengajar di kelas {$classroom->name} hari ini.", 422);
        }

        // ── Cek presensi yang sudah ada ──────────────────────────────────────
        $existingAttendance = ClassAttendance::where('user_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->where('date', $today)
            ->where('period', $schedule->period)
            ->first();

        // Status kehadiran berdasarkan waktu jadwal
        $scheduleStart = Carbon::parse($schedule->start_time);
        $status        = $now->greaterThan($scheduleStart) ? 'Terlambat' : 'Hadir';

        if (!$existingAttendance) {
            // ── SCAN MASUK ───────────────────────────────────────────────────
            ClassAttendance::create([
                'user_id'              => auth()->id(),
                'classroom_id'         => $classroom->id,
                'teaching_schedule_id' => $schedule->id,
                'date'                 => $today,
                'period'               => $schedule->period,
                'check_in_time'        => $currentTime,
                'status'               => $status,
            ]);

            $message = "✅ Presensi MASUK kelas {$classroom->name} berhasil!\nJam Pelajaran: {$schedule->period}\nWaktu: {$now->format('H:i')} WIB\nStatus: {$status}";
            return $this->respond($isAjax, true, $message);

        } elseif ($existingAttendance->check_in_time && !$existingAttendance->check_out_time) {
            // ── SCAN KELUAR ──────────────────────────────────────────────────
            $existingAttendance->update(['check_out_time' => $currentTime]);

            $message = "✅ Presensi KELUAR kelas {$classroom->name} berhasil!\nJam Pelajaran: {$schedule->period}\nWaktu: {$now->format('H:i')} WIB";
            return $this->respond($isAjax, true, $message);

        } else {
            return $this->respond($isAjax, false, "Anda sudah menyelesaikan presensi untuk kelas {$classroom->name} jam pelajaran ke-{$schedule->period}.", 422);
        }
    }

    /**
     * Riwayat presensi kelas guru
     */
    public function history()
    {
        $attendances = ClassAttendance::where('user_id', auth()->id())
            ->with(['classroom', 'teachingSchedule.subject'])
            ->orderBy('date', 'desc')
            ->orderBy('period', 'desc')
            ->paginate(20);

        return view('class-attendance.history', compact('attendances'));
    }

    /**
     * Helper: return JSON atau redirect+flash tergantung jenis request.
     */
    private function respond(bool $isAjax, bool $success, string $message, int $statusCode = 200)
    {
        if ($isAjax) {
            return response()->json(
                ['success' => $success, 'message' => $message],
                $success ? 200 : $statusCode
            );
        }

        return $success
            ? back()->with('success', $message)
            : back()->with('error', $message);
    }
}
