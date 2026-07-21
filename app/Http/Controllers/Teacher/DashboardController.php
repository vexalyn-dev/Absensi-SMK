<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSchedule;
use App\Models\TeacherSchedule;
use App\Models\Attendance;
use App\Models\ClassAttendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayDayOfWeek = $today->dayOfWeek;

        // Jadwal mengajar hari ini (with class attendances)
        $todaySchedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject', 'classAttendances' => function($query) use ($user, $today) {
                $query->where('user_id', $user->id)
                      ->whereDate('date', $today);
            }])
            ->orderBy('start_time')
            ->get();

        // JADWAL KERJA (Work Schedule)
        $workSchedule = TeacherSchedule::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->sortBy('day_of_week');

        // Absensi hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Presensi kelas hari ini
        $todayClassAttendances = ClassAttendance::with('classroom')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();

        // Statistik bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = [
            'hadir' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'Hadir')
                ->count(),
            'terlambat' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'Terlambat')
                ->count(),
            'izin' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->whereIn('status', ['Izin', 'Sakit'])
                ->count(),
            'alpha' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'Alpha')
                ->count(),
        ];

        return view('teacher.dashboard', compact(
            'todaySchedules',
            'workSchedule',
            'todayAttendance',
            'todayClassAttendances',
            'stats'
        ));
    }
}