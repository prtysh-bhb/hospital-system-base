<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'prescription_number',
        'appointment_id',
        'patient_id',
        'doctor_id',
        'diagnosis',
        'medications',
        'instructions',
        'follow_up_date',
        'notes',
    ];

    protected $casts = [
        'medications' => 'array',
        'follow_up_date' => 'date',
    ];

    /**
     * Get the appointment for the prescription.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the patient for the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor for the prescription.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Scope a query to include prescriptions with follow-up dates.
     */
    public function scopeWithFollowUp($query)
    {
        return $query->whereNotNull('follow_up_date');
    }
}
