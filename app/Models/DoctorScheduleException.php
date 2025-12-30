<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorScheduleException extends Model
{
    use HasFactory;
    protected $table = 'doctor_schedule_exceptions';
    protected $fillable = [
        'doctor_id',
        'exception_date',
        'is_available',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'exception_date' => 'date',
        'is_available' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the doctor that owns the schedule exception.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Scope a query to filter by date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('exception_date', $date);
    }

    /**
     * Scope a query to only include unavailable exceptions.
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }
}
