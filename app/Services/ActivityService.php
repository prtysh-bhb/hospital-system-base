<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityService
{
    /**
     * Log an activity
     *
     * @param  string  $action  - create, update, delete, status_change, etc.
     * @param  string  $modelType  - The model class name
     * @param  int|null  $modelId  - The model ID
     * @param  array  $oldValues  - Previous values (for updates)
     * @param  array  $newValues  - New values
     * @param  string|null  $description  - Human-readable description
     */
    public static function log(
        string $action,
        string $modelType,
        ?int $modelId = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => array_merge($newValues, ['description' => $description]),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get recent activities for dashboard
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRecentActivities(int $limit = 10)
    {
        $activities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'action' => $activity->action,
                'model_type' => self::getModelDisplayName($activity->model_type),
                'model_id' => $activity->model_id,
                'description' => self::generateDescription($activity),
                'user_name' => $activity->user ? $activity->user->full_name : 'System',
                'time_ago' => $activity->created_at->diffForHumans(),
                'created_at' => $activity->created_at->format('M d, Y h:i A'),
                'icon' => self::getActionIcon($activity->action),
                'color' => self::getActionColor($activity->action),
            ];
        });
    }

    /**
     * Get human-readable model name
     */
    private static function getModelDisplayName(string $modelType): string
    {
        $map = [
            'App\\Models\\User' => 'User',
            'App\\Models\\Appointment' => 'Appointment',
            'App\\Models\\PatientProfile' => 'Patient',
            'App\\Models\\DoctorProfile' => 'Doctor',
            'App\\Models\\Prescription' => 'Prescription',
            'App\\Models\\DoctorSchedule' => 'Doctor Schedule',
            'App\\Models\\Notification' => 'Notification',
        ];

        return $map[$modelType] ?? class_basename($modelType);
    }

    /**
     * Generate human-readable description
     */
    private static function generateDescription($activity): string
    {
        // If custom description was provided, use it
        if (isset($activity->new_values['description']) && $activity->new_values['description']) {
            return $activity->new_values['description'];
        }

        $modelName = self::getModelDisplayName($activity->model_type);
        $action = $activity->action;

        // Try to get a name from the new_values
        $name = $activity->new_values['name']
            ?? $activity->new_values['first_name']
            ?? $activity->new_values['appointment_number']
            ?? null;

        $nameStr = $name ? " \"{$name}\"" : " #{$activity->model_id}";

        return match ($action) {
            'create' => "New {$modelName}{$nameStr} was created",
            'update' => "{$modelName}{$nameStr} was updated",
            'delete' => "{$modelName}{$nameStr} was deleted",
            'status_change' => "{$modelName}{$nameStr} status changed to ".($activity->new_values['status'] ?? 'unknown'),
            'login' => 'User logged in',
            'logout' => 'User logged out',
            default => "{$modelName}{$nameStr} - {$action}",
        };
    }

    /**
     * Get icon for action type
     */
    private static function getActionIcon(string $action): string
    {
        return match ($action) {
            'create' => 'plus-circle',
            'update' => 'pencil',
            'delete' => 'trash',
            'status_change' => 'refresh',
            'login' => 'login',
            'logout' => 'logout',
            default => 'information-circle',
        };
    }

    /**
     * Get color for action type
     */
    private static function getActionColor(string $action): string
    {
        return match ($action) {
            'create' => 'green',
            'update' => 'amber',
            'delete' => 'red',
            'status_change' => 'sky',
            'login' => 'indigo',
            'logout' => 'gray',
            default => 'gray',
        };
    }
}
