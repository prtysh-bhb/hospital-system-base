<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'status',
        'appointment_type',
        'reason_for_visit',
        'symptoms',
        'notes',
        'cancellation_reason',
        'booked_by',
        'booked_via',
        'reminder_sent',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'duration_minutes' => 'integer',
        'reminder_sent' => 'boolean',
    ];

    protected $appends = ['formatted_date', 'formatted_time'];

    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('d-m-Y');
    }

    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->appointment_time)->format('h:i A');
    }

    /**
     * Get the patient for the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor for the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the user who booked the appointment.
     */
    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    /**
     * Get the prescriptions for the appointment.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Scope a query to only include appointments with a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope a query to only include today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->where('appointment_date', now()->toDateString());
    }
}
