<?php

namespace App\Services\Admin;

use App\Events\AppointmentRescheduled;
use App\Models\Appointment;
use App\Services\AppointmentSlotService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentRescheduleService
{
    protected AppointmentSlotService $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Reschedule an appointment to a new date/time.
     *
     * @param int $appointmentId
     * @param array $newData ['appointment_date', 'appointment_time']
     * @return array
     */
    public function rescheduleAppointment(int $appointmentId, array $newData): array
    {
        try {
            DB::beginTransaction();

            // Get the appointment
            $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($appointmentId);

            // Store old data for event and audit
            $oldData = [
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
            ];

            // Validate new time slot is available
            $slotValidation = $this->slotService->validateAppointmentTime(
                $appointment->doctor_id,
                $newData['appointment_date'],
                $newData['appointment_time'],
                $appointmentId // Exclude current appointment from validation
            );

            if (! $slotValidation['valid']) {
                return [
                    'success' => false,
                    'message' => $slotValidation['message'],
                ];
            }

            // Parse new appointment time
            $newAppointmentDateTime = Carbon::parse($newData['appointment_date'].' '.$newData['appointment_time']);

            // Update appointment
            $appointment->update([
                'appointment_date' => $newData['appointment_date'],
                'appointment_time' => $newAppointmentDateTime->format('H:i:s'),
                'status' => 'pending', // Reset to pending when rescheduled
            ]);

            // Prepare data for event
            $newDataForEvent = [
                'date' => $newData['appointment_date'],
                'time' => $newAppointmentDateTime->format('H:i:s'),
            ];

            // Fire rescheduled event
            event(new AppointmentRescheduled(
                $appointment->fresh(['patient', 'doctor']),
                $oldData,
                $newDataForEvent,
                Auth::user()
            ));

            DB::commit();

            Log::info('Appointment rescheduled successfully', [
                'appointment_id' => $appointmentId,
                'old_date' => $oldData['date'],
                'old_time' => $oldData['time'],
                'new_date' => $newDataForEvent['date'],
                'new_time' => $newDataForEvent['time'],
                'rescheduled_by' => Auth::id(),
            ]);

            return [
                'success' => true,
                'message' => 'Appointment rescheduled successfully',
                'appointment' => $appointment->fresh(['patient', 'doctor']),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error rescheduling appointment', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reschedule appointment: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get available slots for rescheduling.
     *
     * @param int $doctorId
     * @param string $date
     * @param int|null $excludeAppointmentId
     * @return array
     */
    public function getAvailableSlotsForReschedule(int $doctorId, string $date, ?int $excludeAppointmentId = null): array
    {
        return $this->slotService->getAvailableSlots($doctorId, $date, $excludeAppointmentId);
    }

    /**
     * Validate if rescheduling is allowed for an appointment.
     *
     * @param Appointment $appointment
     * @return array
     */
    public function canReschedule(Appointment $appointment): array
    {
        // Can't reschedule completed or cancelled appointments
        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return [
                'can_reschedule' => false,
                'reason' => "Cannot reschedule {$appointment->status} appointments",
            ];
        }

        // Can't reschedule appointments in the past
        $appointmentDateTime = Carbon::parse($appointment->appointment_date.' '.$appointment->appointment_time);
        if ($appointmentDateTime->isPast()) {
            return [
                'can_reschedule' => false,
                'reason' => 'Cannot reschedule past appointments',
            ];
        }

        return [
            'can_reschedule' => true,
            'reason' => null,
        ];
    }

    /**
     * Bulk reschedule appointments (e.g., when doctor is unavailable).
     *
     * @param array $appointmentIds
     * @param array $newData
     * @return array
     */
    public function bulkReschedule(array $appointmentIds, array $newData): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($appointmentIds as $appointmentId) {
            $result = $this->rescheduleAppointment($appointmentId, $newData);

            if ($result['success']) {
                $results['success'][] = $appointmentId;
            } else {
                $results['failed'][] = [
                    'appointment_id' => $appointmentId,
                    'error' => $result['message'],
                ];
            }
        }

        return $results;
    }
}
