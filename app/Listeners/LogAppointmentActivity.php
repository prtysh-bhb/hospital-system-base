<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentRescheduled;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogAppointmentActivity
{
    /**
     * Log appointment booked activity.
     */
    public function handleBooked(AppointmentBooked $event): void
    {
        $this->createAuditLog(
            $event->appointment->id,
            'appointment_booked',
            'Appointment booked',
            [
                'appointment_number' => $event->appointment->appointment_number,
                'booked_via' => $event->bookedVia,
                'patient_id' => $event->appointment->patient_id,
                'doctor_id' => $event->appointment->doctor_id,
                'date' => $event->appointment->appointment_date,
                'time' => $event->appointment->appointment_time,
            ]
        );
    }

    /**
     * Log appointment confirmed activity.
     */
    public function handleConfirmed(AppointmentConfirmed $event): void
    {
        $this->createAuditLog(
            $event->appointment->id,
            'appointment_confirmed',
            'Appointment confirmed',
            [
                'appointment_number' => $event->appointment->appointment_number,
                'confirmed_by' => $event->confirmedBy ? $event->confirmedBy->id : null,
            ]
        );
    }

    /**
     * Log appointment cancelled activity.
     */
    public function handleCancelled(AppointmentCancelled $event): void
    {
        $this->createAuditLog(
            $event->appointment->id,
            'appointment_cancelled',
            'Appointment cancelled',
            [
                'appointment_number' => $event->appointment->appointment_number,
                'reason' => $event->reason,
                'cancelled_by' => $event->cancelledBy ? $event->cancelledBy->id : null,
            ]
        );
    }

    /**
     * Log appointment rescheduled activity.
     */
    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        $this->createAuditLog(
            $event->appointment->id,
            'appointment_rescheduled',
            'Appointment rescheduled',
            [
                'appointment_number' => $event->appointment->appointment_number,
                'old_data' => $event->oldData,
                'new_data' => $event->newData,
                'rescheduled_by' => $event->rescheduledBy ? $event->rescheduledBy->id : null,
            ]
        );
    }

    /**
     * Create audit log entry.
     */
    private function createAuditLog(int $appointmentId, string $action, string $description, array $changes = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => 'App\Models\Appointment',
            'model_id' => $appointmentId,
            'description' => $description,
            'changes' => json_encode($changes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
