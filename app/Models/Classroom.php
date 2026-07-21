<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',
        'major',
        'level',
        'type',
        'location_type',
        'is_shared',
        'capacity',
        'description',
        'is_active',
        'qr_token',
        'qr_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($classroom) {
            if (empty($classroom->qr_token)) {
                $classroom->qr_token = Str::uuid()->toString();
            }
        });
    }

    public function teachingSchedules()
    {
        return $this->hasMany(TeachingSchedule::class);
    }

    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    /**
     * Generate data untuk QR Code
     */
    public function getQrDataAttribute()
    {
        return json_encode([
            'type' => 'classroom',
            'classroom_id' => $this->id,
            'token' => $this->qr_token
        ]);
    }

    /**
     * Get class level badge color
     */
    public function getClassLevelColorAttribute()
    {
        $level = $this->level ?? $this->class_level;
        $colors = [
            'X' => 'from-blue-500 to-cyan-500',
            'XI' => 'from-violet-500 to-purple-500',
            'XII' => 'from-emerald-500 to-teal-500'
        ];
        return $colors[$level] ?? 'from-slate-500 to-gray-500';
    }

    /**
     * Accessor: Extract major code from code field (e.g. X-RPL => RPL)
     */
    public function getMajorCodeAttribute()
    {
        if ($this->attributes['code'] ?? null) {
            return preg_replace('/^(XII|XI|X)-/', '', $this->attributes['code']);
        }
        return null;
    }

    /**
     * Mutator: Auto-uppercase code on set
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}