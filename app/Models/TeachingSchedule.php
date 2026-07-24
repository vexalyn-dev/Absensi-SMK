<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeachingSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'subject_id',
        'day_of_week',
        'period',
        'start_time',
        'end_time',
        'academic_year',
        'semester',
        'is_active'
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'period' => 'integer',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    public static function getDayName($dayOfWeek)
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayOfWeek] ?? 'Unknown';
    }

    /**
     * Ambil jadwal mengajar hari ini untuk guru
     */
    public static function getTodaySchedules($userId)
    {
        $today = Carbon::now()->dayOfWeek;

        return self::with(['classroom', 'subject'])
            ->where('user_id', $userId)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->orderBy('period')
            ->get();
    }

    /**
     * Cari jadwal yang cocok untuk scan saat ini
     */
    public static function findMatchingSchedule($userId, $classroomId, $period = null)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $today = $now->dayOfWeek;

        $query = self::where('user_id', $userId)
            ->where('classroom_id', $classroomId)
            ->where('day_of_week', $today)
            ->where('is_active', true);

        if ($period) {
            $query->where('period', $period);
        }

        // Cari jadwal yang waktunya mencakup saat ini (toleransi 15 menit)
        return $query->where(function ($q) use ($currentTime) {
            $q->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime);
        })->orWhere(function ($q) use ($currentTime) {
            // Toleransi 15 menit sebelum jadwal
            $q->whereRaw('TIME_SUB(start_time, INTERVAL 15 MINUTE) <= ?', [$currentTime])
                ->where('start_time', '>=', $currentTime);
        })->first();
    }
}