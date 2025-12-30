<?php

namespace App\Models;

use App\Observers\AppointmentObserver;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    protected $table = 'appointments';
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
        'cancelled_at',
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
        if (empty($this->appointment_date)) {
            return null;
        }

        try {
            return $this->appointment_date->format('d-m-Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFormattedTimeAttribute()
    {
        if (empty($this->appointment_time)) {
            return null;
        }

        try {
            return Carbon::parse($this->appointment_time)->format('h:i A');
        } catch (\Exception $e) {
            return null;
        }
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
