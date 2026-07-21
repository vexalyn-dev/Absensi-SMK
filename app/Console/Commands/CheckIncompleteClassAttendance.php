<?php

namespace App\Console\Commands;

use App\Models\ClassAttendance;
use App\Models\TeacherSchedule;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckIncompleteClassAttendance extends Command
{
    protected $signature = 'attendance:check-incomplete';
    protected $description = 'Check incomplete class attendance and notify admin';

    public function handle()
    {
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;
        $now = Carbon::now();

        // Ambil semua jadwal hari ini
        $schedules = TeacherSchedule::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['user', 'classroom', 'subject'])
            ->get();

        $incompleteCount = 0;

        foreach ($schedules as $schedule) {
            $attendance = ClassAttendance::where('user_id', $schedule->user_id)
                ->where('classroom_id', $schedule->classroom_id)
                ->where('period', $schedule->period)
                ->whereDate('date', $today)
                ->first();

            // Cek kalau sudah lewat jam selesai tapi belum scan keluar
            $endTime = Carbon::parse($schedule->end_time);
            
            if ($now->gt($endTime->addMinutes(15))) { // Grace period 15 menit
                if (!$attendance || !$attendance->check_out_time) {
                    $incompleteCount++;

                    // Notifikasi ke guru
                    NotificationHelper::send(
                        $schedule->user,
                        'warning',
                        'Presensi Kelas Tidak Lengkap',
                        "Anda belum scan keluar di kelas {$schedule->classroom->name} ({$schedule->subject->name}). Silakan hubungi admin jika ada kendala.",
                        route('teacher.class-attendance'),
                        'alert-triangle',
                        'bg-yellow-100 text-yellow-600'
                    );

                    // Notifikasi ke semua admin
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        NotificationHelper::send(
                            $admin,
                            'warning',
                            'Presensi Tidak Lengkap',
                            "{$schedule->user->name} belum scan keluar di kelas {$schedule->classroom->name} ({$schedule->subject->name}) - Jam ke-{$schedule->period}",
                            route('admin.class-attendance.monitoring'),
                            'alert-triangle',
                            'bg-yellow-100 text-yellow-600'
                        );
                    }

                    $this->info("Notifikasi dikirim: {$schedule->user->name} - {$schedule->classroom->name}");
                }
            }
        }

        $this->info("Selesai! {$incompleteCount} presensi tidak lengkap.");
        return Command::SUCCESS;
    }
}