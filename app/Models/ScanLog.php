<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanLog extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'mode',
        'status',
        'message',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'device_info',
        'metadata',
        'scanned_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'scanned_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}