<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSchedule;
use App\Models\TeacherSchedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::now()->dayOfWeek;

        $schedules = TeachingSchedule::with(['classroom', 'subject'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('period')
            ->get()
            ->groupBy('day_of_week');

        $dayNames = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'
        ];

        return view('teacher.schedule', compact('schedules', 'dayNames', 'today'));
    }

    public function workSchedule()
    {
        $user = auth()->user();
        $today = Carbon::now()->dayOfWeek;

        $workSchedules = TeacherSchedule::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get();

        $dayNames = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
        ];

        // Hitung total jam kerja mingguan
        $totalWeeklyHours = $workSchedules->sum(function($schedule) {
            return Carbon::parse($schedule->start_time)->diffInHours(Carbon::parse($schedule->end_time));
        });

        return view('teacher.work-schedule', compact('workSchedules', 'dayNames', 'today', 'totalWeeklyHours'));
    }
}