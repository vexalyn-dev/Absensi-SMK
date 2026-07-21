<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSchedule;
use Carbon\Carbon;

class WorkScheduleController extends Controller
{
    public function index()
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

        // Hitung total jam kerja mingguan dan format data schedule
        $totalWeeklyHours = 0;
        $scheduleData = [];
        
        foreach($workSchedules as $schedule) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $duration = $start->diffInMinutes($end);
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            
            $totalWeeklyHours += $duration / 60;
            
            $scheduleData[] = [
                'day_of_week' => $schedule->day_of_week,
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'duration_minutes' => $duration,
                'duration_hours' => $hours,
                'duration_minutes_left' => $minutes,
                'duration_text' => $hours . ' jam ' . ($minutes > 0 ? $minutes . ' menit' : ''),
                'progress_percent' => min(($duration / 720) * 100, 100),
                'is_today' => $schedule->day_of_week === $today
            ];
        }

        // Hitung jumlah hari kerja (unique days)
        $workDays = $workSchedules->pluck('day_of_week')->unique()->count();

        return view('teacher.work-schedule.index', compact('scheduleData', 'dayNames', 'today', 'totalWeeklyHours', 'workDays'));
    }
}
