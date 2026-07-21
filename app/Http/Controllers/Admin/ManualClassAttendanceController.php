<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\Classroom;
use App\Models\TeachingSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManualClassAttendanceController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;

        $schedules = TeachingSchedule::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['user', 'classroom', 'subject'])
            ->orderBy('start_time')
            ->get();

        // Ambil attendance hari ini untuk setiap jadwal
        $schedules->each(function($schedule) use ($today) {
            $schedule->todayAttendance = ClassAttendance::where('user_id', $schedule->user_id)
                ->where('classroom_id', $schedule->classroom_id)
                ->where('period', $schedule->period)
                ->whereDate('date', $today)
                ->first();
        });

        $teachers   = User::where('role', 'guru')->where('is_active', true)->orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.class-attendance.manual', compact('schedules', 'teachers', 'classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'classroom_id'   => 'required|exists:classrooms,id',
            'period'         => 'required|integer|min:1|max:12',
            'date'           => 'required|date',
            'check_in_time'  => 'required|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'status'         => 'required|in:Hadir,Terlambat',
            'notes'          => 'nullable|string|max:500',
        ]);

        $date      = Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        // Cek apakah guru punya jadwal di hari itu
        $hasSchedule = TeachingSchedule::where('user_id', $validated['user_id'])
            ->where('day_of_week', $dayOfWeek)
            ->where('classroom_id', $validated['classroom_id'])
            ->where('period', $validated['period'])
            ->where('is_active', true)
            ->exists();

        if (!$hasSchedule) {
            return back()->with('error', 'Guru tidak memiliki jadwal di kelas ini pada hari tersebut!');
        }

        // Cek apakah sudah ada record
        $existing = ClassAttendance::where('user_id', $validated['user_id'])
            ->where('classroom_id', $validated['classroom_id'])
            ->where('period', $validated['period'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Presensi untuk jadwal ini sudah ada!');
        }

        $checkIn  = Carbon::parse($validated['date'])->setTimeFromTimeString($validated['check_in_time']);
        $checkOut = $validated['check_out_time']
            ? Carbon::parse($validated['date'])->setTimeFromTimeString($validated['check_out_time'])
            : null;

        ClassAttendance::create([
            'user_id'        => $validated['user_id'],
            'classroom_id'   => $validated['classroom_id'],
            'period'         => $validated['period'],
            'date'           => $validated['date'],
            'check_in_time'  => $checkIn,
            'check_out_time' => $checkOut,
            'status'         => $validated['status'],
            'scan_method'    => 'manual_admin',
            'notes'          => $validated['notes'] ?? null,
        ]);

        $teacher   = User::find($validated['user_id']);
        $classroom = Classroom::find($validated['classroom_id']);

        \App\Helpers\NotificationHelper::send(
            $teacher,
            'info',
            'Presensi Kelas Ditambahkan Admin',
            "Admin telah menambahkan presensi Anda di kelas {$classroom->name} pada tanggal {$date->format('d M Y')}",
            route('teacher.class-attendance'),
            'info',
            'bg-blue-100 text-blue-600'
        );

        return back()->with('success', 'Presensi berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $attendance = ClassAttendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Presensi berhasil dihapus!');
    }
}