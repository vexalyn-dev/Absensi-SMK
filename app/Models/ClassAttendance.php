<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ClassAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'selected_classroom_id',
        'subject_id',
        'teaching_schedule_id',
        'period',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'scan_method',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function selectedClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'selected_classroom_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teachingSchedule(): BelongsTo
    {
        return $this->belongsTo(TeachingSchedule::class);
    }

    // Helper: Cek apakah presensi lengkap (ada IN & OUT)
    public function isComplete()
    {
        return !is_null($this->check_in_time) && !is_null($this->check_out_time);
    }

    // Helper: Hitung durasi mengajar (menit)
    public function getDurationMinutes()
    {
        if (!$this->check_in_time || !$this->check_out_time) return 0;
        return $this->check_in_time->diffInMinutes($this->check_out_time);
    }
}
