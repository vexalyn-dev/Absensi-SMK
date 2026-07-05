<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;

class TeachingScheduleController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->with(['teachingSchedules.classroom', 'teachingSchedules.subject'])
            ->get();

        return view('teaching-schedules.index', compact('teachers'));
    }

    public function edit(User $teacher)
    {
        $classrooms = Classroom::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        $schedules = TeachingSchedule::where('user_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('period')
            ->get();

        return view('teaching-schedules.edit', compact('teacher', 'classrooms', 'subjects', 'schedules'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.classroom_id' => 'required|exists:classrooms,id',
            'schedules.*.subject_id' => 'nullable|exists:subjects,id',
            'schedules.*.period' => 'required|integer|min:1|max:15',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
            'schedules.*.schedule_id' => 'nullable|exists:teaching_schedules,id',
            'delete_schedules' => 'nullable|array',
            'delete_schedules.*' => 'exists:teaching_schedules,id',
        ]);

        \DB::beginTransaction();
        try {
            // 1. HAPUS JADWAL YANG DI-MARK FOR DELETE
            if (!empty($validated['delete_schedules'])) {
                TeachingSchedule::whereIn('id', $validated['delete_schedules'])
                    ->where('user_id', $teacher->id)
                    ->delete();
            }

            // 2. PROSES SEMUA SCHEDULES DARI FORM
            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $key => $scheduleData) {
                    // Check if end_time is after start_time
                    if (strtotime($scheduleData['end_time']) <= strtotime($scheduleData['start_time'])) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "schedules.{$key}.end_time" => 'Jam pulang harus lebih besar dari jam masuk.'
                        ]);
                    }

                    // Cek apakah ini edit jadwal existing atau tambah baru
                    if (isset($scheduleData['schedule_id']) && $scheduleData['schedule_id']) {
                        // UPDATE jadwal existing
                        TeachingSchedule::where('id', $scheduleData['schedule_id'])
                            ->where('user_id', $teacher->id)
                            ->update([
                                'day_of_week' => $scheduleData['day_of_week'],
                                'classroom_id' => $scheduleData['classroom_id'],
                                'subject_id' => $scheduleData['subject_id'] ?? null,
                                'period' => $scheduleData['period'],
                                'start_time' => $scheduleData['start_time'],
                                'end_time' => $scheduleData['end_time'],
                            ]);
                    } else {
                        // CREATE jadwal baru
                        TeachingSchedule::create([
                            'user_id' => $teacher->id,
                            'day_of_week' => $scheduleData['day_of_week'],
                            'classroom_id' => $scheduleData['classroom_id'],
                            'subject_id' => $scheduleData['subject_id'] ?? null,
                            'period' => $scheduleData['period'],
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time'],
                            'is_active' => true,
                        ]);
                    }
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

        return redirect()->route('teaching-schedules.index')
            ->with('success', 'Jadwal mengajar berhasil diperbarui');
    }
}