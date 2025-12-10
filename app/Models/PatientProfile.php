<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientProfile extends Model
{
    use HasFactory, LogsActivity ,SoftDeletes;

    protected $fillable = [
        'user_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_group',
        'allergies',
        'medical_history',
        'current_medications',
        'insurance_provider',
        'insurance_number',
        'delete_at',
    ];

    /**
     * Get the user that owns the patient profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'user_id');
    }

    /**
     * Get the prescriptions for the patient.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id', 'user_id');
    }
}
