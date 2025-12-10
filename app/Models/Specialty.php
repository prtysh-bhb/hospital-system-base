<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialty extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the doctors for this specialty.
     */
    public function doctorProfiles(): HasMany
    {
        return $this->hasMany(DoctorProfile::class);
    }

    /**
     * Scope a query to only include active specialties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
