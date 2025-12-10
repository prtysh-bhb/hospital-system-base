<?php

namespace App\Traits;

use App\Services\ActivityService;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        // Log when a model is created
        static::created(function ($model) {
            $description = self::getCreateDescription($model);
            ActivityService::log(
                'create',
                get_class($model),
                $model->id,
                [],
                $model->getActivityLogAttributes(),
                $description
            );
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $original = $model->getOriginal();

            // Filter out sensitive fields
            $sensitiveFields = ['password', 'remember_token', 'email_verified_at'];
            $oldValues = array_diff_key($original, array_flip($sensitiveFields));
            $newValues = array_diff_key($dirty, array_flip($sensitiveFields));

            // Check if status changed for special logging
            $action = isset($dirty['status']) ? 'status_change' : 'update';

            $description = self::getUpdateDescription($model, $dirty);

            ActivityService::log(
                $action,
                get_class($model),
                $model->id,
                array_intersect_key($oldValues, $newValues),
                array_merge($newValues, $model->getActivityLogAttributes()),
                $description
            );
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $description = self::getDeleteDescription($model);
            ActivityService::log(
                'delete',
                get_class($model),
                $model->id,
                $model->getActivityLogAttributes(),
                [],
                $description
            );
        });
    }

    /**
     * Get attributes to include in activity log
     */
    public function getActivityLogAttributes(): array
    {
        // Default attributes to log - can be overridden in individual models
        $attributes = [];

        if (isset($this->first_name)) {
            $attributes['first_name'] = $this->first_name;
        }
        if (isset($this->last_name)) {
            $attributes['last_name'] = $this->last_name;
        }
        if (isset($this->name)) {
            $attributes['name'] = $this->name;
        }
        if (isset($this->appointment_number)) {
            $attributes['appointment_number'] = $this->appointment_number;
        }
        if (isset($this->status)) {
            $attributes['status'] = $this->status;
        }
        if (isset($this->role)) {
            $attributes['role'] = $this->role;
        }

        return $attributes;
    }

    /**
     * Generate description for create action
     */
    protected static function getCreateDescription($model): string
    {
        $className = class_basename(get_class($model));

        return match ($className) {
            'User' => self::getUserCreateDescription($model),
            'Appointment' => "New appointment {$model->appointment_number} booked",
            'PatientProfile' => 'New patient profile created',
            'DoctorProfile' => 'New doctor profile created',
            'Prescription' => 'New prescription created for appointment',
            'DoctorSchedule' => 'Doctor schedule updated',
            default => "New {$className} created",
        };
    }

    /**
     * Generate description for update action
     */
    protected static function getUpdateDescription($model, array $dirty): string
    {
        $className = class_basename(get_class($model));

        // Status change
        if (isset($dirty['status'])) {
            return match ($className) {
                'Appointment' => "Appointment {$model->appointment_number} status changed to {$dirty['status']}",
                'User' => self::getUserStatusDescription($model, $dirty['status']),
                default => "{$className} status changed to {$dirty['status']}",
            };
        }

        return match ($className) {
            'User' => self::getUserUpdateDescription($model),
            'Appointment' => "Appointment {$model->appointment_number} was updated",
            'PatientProfile' => 'Patient profile updated',
            'DoctorProfile' => 'Doctor profile updated',
            'DoctorSchedule' => 'Doctor schedule modified',
            default => "{$className} was updated",
        };
    }

    /**
     * Generate description for delete action
     */
    protected static function getDeleteDescription($model): string
    {
        $className = class_basename(get_class($model));

        return match ($className) {
            'User' => self::getUserDeleteDescription($model),
            'Appointment' => "Appointment {$model->appointment_number} was deleted",
            'PatientProfile' => 'Patient profile was removed',
            'DoctorProfile' => 'Doctor profile was removed',
            default => "{$className} was deleted",
        };
    }

    /**
     * User-specific description helpers
     */
    protected static function getUserCreateDescription($model): string
    {
        $name = $model->full_name ?? "{$model->first_name} {$model->last_name}";

        return match ($model->role) {
            'patient' => "New patient \"{$name}\" registered",
            'doctor' => "New doctor \"{$name}\" added",
            'frontdesk' => "New frontdesk staff \"{$name}\" added",
            'admin' => "New admin \"{$name}\" added",
            default => "New user \"{$name}\" created",
        };
    }

    protected static function getUserUpdateDescription($model): string
    {
        $name = $model->full_name ?? "{$model->first_name} {$model->last_name}";

        return match ($model->role) {
            'patient' => "Patient \"{$name}\" profile updated",
            'doctor' => "Doctor \"{$name}\" profile updated",
            'frontdesk' => "Frontdesk staff \"{$name}\" updated",
            default => "User \"{$name}\" updated",
        };
    }

    protected static function getUserDeleteDescription($model): string
    {
        $name = $model->full_name ?? "{$model->first_name} {$model->last_name}";

        return match ($model->role) {
            'patient' => "Patient \"{$name}\" was removed",
            'doctor' => "Doctor \"{$name}\" was removed",
            'frontdesk' => "Frontdesk staff \"{$name}\" was removed",
            default => "User \"{$name}\" was deleted",
        };
    }

    protected static function getUserStatusDescription($model, string $status): string
    {
        $name = $model->full_name ?? "{$model->first_name} {$model->last_name}";

        return match ($model->role) {
            'patient' => "Patient \"{$name}\" status changed to {$status}",
            'doctor' => "Doctor \"{$name}\" status changed to {$status}",
            default => "User \"{$name}\" status changed to {$status}",
        };
    }
}
