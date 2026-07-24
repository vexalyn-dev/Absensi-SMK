<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Filter parameters
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $type = $request->input('type', 'daily'); // daily atau class

        if ($type === 'daily') {
            // Riwayat absensi harian
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(15);

            // Statistics
            $stats = [
                'total' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->count(),
                'hadir' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Hadir')
                    ->count(),
                'terlambat' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Terlambat')
                    ->count(),
                'izin' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Izin')
                    ->count(),
                'alpha' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Alpha')
                    ->count(),
            ];

            return view('teacher.history', compact('attendances', 'stats', 'startDate', 'endDate', 'type'));
        } else {
            // Riwayat presensi kelas
            $classAttendances = ClassAttendance::with(['classroom', 'teachingSchedule.subject'])
                ->where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('period', 'asc')
                ->paginate(15);

            // Statistics
            $stats = [
                'total' => ClassAttendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->count(),
                'completed' => ClassAttendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotNull('check_out_time')
                    ->count(),
                'hadir' => ClassAttendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Hadir')
                    ->count(),
                'terlambat' => ClassAttendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Terlambat')
                    ->count(),
            ];

            return view('teacher.history-class', compact('classAttendances', 'stats', 'startDate', 'endDate', 'type'));
        }
    }

    public function getData(Request $request)
    {
        $user = auth()->user();
        $type = $request->input('type', 'daily');
        $page = $request->input('page', 1);

        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->toDateString();

        if ($type === 'daily') {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(10, ['*'], 'page', $page);

            $attendances->getCollection()->transform(function ($att) use ($user) {
                $date = Carbon::parse($att->date);
                $dayOfWeek = $date->dayOfWeek;

                $schedules = TeachingSchedule::where('user_id', $user->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->get();

                $totalHours = 0;
                foreach ($schedules as $schedule) {
                    $start = Carbon::parse($schedule->start_time);
                    $end = Carbon::parse($schedule->end_time);
                    $totalHours += $start->diffInHours($end);
                }

                $att->teaching_duration = $totalHours;
                $att->date_formatted = $date->format('d M Y');
                $att->day_name = $date->locale('id')->isoFormat('dddd');
                $att->check_in = $att->check_in ? Carbon::parse($att->check_in)->format('H:i') : null;
                $att->check_out = $att->check_out ? Carbon::parse($att->check_out)->format('H:i') : null;

                return $att;
            });

            $stats = [
                'total' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->count(),
                'hadir' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Hadir')
                    ->count(),
                'terlambat' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Terlambat')
                    ->count(),
                'izin' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Izin')
                    ->count(),
                'alpha' => Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'Alpha')
                    ->count(),
            ];

            return response()->json([
                'attendances' => $attendances,
                'stats' => $stats,
                'links' => $attendances->links()->toHtml(),
                'last_page' => $attendances->lastPage(),
            ]);
        }

        $classAttendances = ClassAttendance::with(['classroom', 'teachingSchedule.subject'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('period', 'asc')
            ->paginate(10, ['*'], 'page', $page);

        $classAttendances->getCollection()->transform(function ($att) {
            $date = Carbon::parse($att->date);
            $att->date_formatted = $date->format('d M Y');
            $att->day_name = $date->locale('id')->isoFormat('dddd');
            $att->classroom_name = $att->classroom->name ?? '-';
            $att->subject_name = $att->teachingSchedule?->subject?->name ?? ($att->subject?->name ?? '-');
            $att->check_in_time = $att->check_in_time ? Carbon::parse($att->check_in_time)->format('H:i') : null;
            $att->check_out_time = $att->check_out_time ? Carbon::parse($att->check_out_time)->format('H:i') : null;

            return $att;
        });

        $stats = [
            'total' => ClassAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->count(),
            'completed' => ClassAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('check_out_time')
                ->count(),
            'hadir' => ClassAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'Hadir')
                ->count(),
            'terlambat' => ClassAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'Terlambat')
                ->count(),
        ];

        return response()->json([
            'attendances' => $classAttendances,
            'stats' => $stats,
            'links' => $classAttendances->links()->toHtml(),
            'last_page' => $classAttendances->lastPage(),
        ]);
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $type = $request->input('type', 'daily');

        if ($type === 'daily') {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            $filename = "Riwayat_Absensi_{$user->name}_{$startDate}_{$endDate}.csv";
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan']);
            
            foreach ($attendances as $att) {
                fputcsv($output, [
                    Carbon::parse($att->date)->format('d-m-Y'),
                    $att->check_in ?? '-',
                    $att->check_out ?? '-',
                    $att->status,
                    $att->notes ?? '-',
                ]);
            }
            
            fclose($output);
            exit;
        } else {
            $classAttendances = ClassAttendance::with(['classroom', 'teachingSchedule.subject'])
                ->where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('period', 'asc')
                ->get();

            $filename = "Riwayat_Presensi_Kelas_{$user->name}_{$startDate}_{$endDate}.csv";
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, ['Tanggal', 'Kelas', 'Mapel', 'Jam Pelajaran', 'Jam Masuk', 'Jam Keluar', 'Status']);
            
            foreach ($classAttendances as $att) {
                fputcsv($output, [
                    Carbon::parse($att->date)->format('d-m-Y'),
                    $att->classroom->name ?? '-',
                    $att->teachingSchedule->subject->name ?? '-',
                    'Jam ke-' . $att->period,
                    $att->check_in_time ?? '-',
                    $att->check_out_time ?? '-',
                    $att->status,
                ]);
            }
            
            fclose($output);
            exit;
        }
    }
}