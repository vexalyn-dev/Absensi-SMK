<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder Check-in (Jam 07:00 Pagi)
Schedule::command('reminder:attendance checkin')
    ->dailyAt('07:00')
    ->withoutOverlapping();

// Reminder Check-out (Jam 15:30 Sore)
Schedule::command('reminder:attendance checkout')
    ->dailyAt('15:30')
    ->withoutOverlapping();

// Dynamic Reminder (Jalankan setiap 1 menit agar presisi)
Schedule::command('reminder:dynamic')->everyMinute()->withoutOverlapping();

// Reminder Jadwal Mengajar (Jalankan setiap 1 menit agar presisi)
Schedule::command('reminder:teaching-schedule')->everyMinute()->withoutOverlapping();

// Reminder Absensi Harian (Jam 08:00 Pagi)
Schedule::command('reminder:daily-attendance')->dailyAt('08:00')->withoutOverlapping();

// Generate Alpha Absences (Jam 23:59 Malam - Akhir Hari)
Schedule::command('attendance:generate-alpha')->dailyAt('23:59')->withoutOverlapping();

// Check Incomplete Class Attendance (Setiap 15 menit)
Schedule::command('attendance:check-incomplete')->everyFifteenMinutes()->withoutOverlapping();
