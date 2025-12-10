<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorProfile extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'specialty_id',
        'qualification',
        'experience_years',
        'consultation_fee',
        'bio',
        'license_number',
        'available_for_booking',
        'deleted_at',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'available_for_booking' => 'boolean',
    ];

    /**
     * Get the user that owns the doctor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialty of the doctor.
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Get the schedules for the doctor.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'user_id');
    }

    /**
     * Get the schedule exceptions for the doctor.
     */
    public function scheduleExceptions(): HasMany
    {
        return $this->hasMany(DoctorScheduleException::class, 'doctor_id', 'user_id');
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'user_id');
    }

    /**
     * Scope a query to only include available doctors.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_for_booking', true);
    }
}
