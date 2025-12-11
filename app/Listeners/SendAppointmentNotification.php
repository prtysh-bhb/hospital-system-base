<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentRescheduled;
use Illuminate\Support\Facades\Log;

class SendAppointmentNotification
{
    /**
     * Handle appointment booked event.
     */
    public function handleBooked(AppointmentBooked $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;
        $doctor = $appointment->doctor;

        // Log the notification
        Log::info('Appointment Booked Notification', [
            'appointment_id' => $appointment->id,
            'appointment_number' => $appointment->appointment_number,
            'patient' => $patient->full_name,
            'doctor' => $doctor->full_name,
            'date' => $appointment->appointment_date,
            'time' => $appointment->appointment_time,
            'booked_via' => $event->bookedVia,
        ]);

        // TODO: Implement WhatsApp notification
        // $this->sendWhatsAppNotification($patient->phone, [
        //     'template' => 'appointment_booked',
        //     'appointment_number' => $appointment->appointment_number,
        //     'doctor_name' => $doctor->full_name,
        //     'date' => $appointment->appointment_date,
        //     'time' => $appointment->appointment_time,
        // ]);

        // TODO: Implement Email notification
        // Mail::to($patient->email)->send(new AppointmentBookedMail($appointment));
    }

    /**
     * Handle appointment confirmed event.
     */
    public function handleConfirmed(AppointmentConfirmed $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;

        Log::info('Appointment Confirmed Notification', [
            'appointment_id' => $appointment->id,
            'appointment_number' => $appointment->appointment_number,
            'patient' => $patient->full_name,
            'confirmed_by' => $event->confirmedBy ? $event->confirmedBy->full_name : 'System',
        ]);

        // TODO: Implement WhatsApp notification
        // $this->sendWhatsAppNotification($patient->phone, [
        //     'template' => 'appointment_confirmed',
        //     'appointment_number' => $appointment->appointment_number,
        // ]);
    }

    /**
     * Handle appointment cancelled event.
     */
    public function handleCancelled(AppointmentCancelled $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;

        Log::info('Appointment Cancelled Notification', [
            'appointment_id' => $appointment->id,
            'appointment_number' => $appointment->appointment_number,
            'patient' => $patient->full_name,
            'reason' => $event->reason,
            'cancelled_by' => $event->cancelledBy ? $event->cancelledBy->full_name : 'System',
        ]);

        // TODO: Implement WhatsApp notification
        // $this->sendWhatsAppNotification($patient->phone, [
        //     'template' => 'appointment_cancelled',
        //     'appointment_number' => $appointment->appointment_number,
        //     'reason' => $event->reason,
        // ]);
    }

    /**
     * Handle appointment rescheduled event.
     */
    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;

        Log::info('Appointment Rescheduled Notification', [
            'appointment_id' => $appointment->id,
            'appointment_number' => $appointment->appointment_number,
            'patient' => $patient->full_name,
            'old_date' => $event->oldData['date'] ?? null,
            'old_time' => $event->oldData['time'] ?? null,
            'new_date' => $event->newData['date'] ?? null,
            'new_time' => $event->newData['time'] ?? null,
            'rescheduled_by' => $event->rescheduledBy ? $event->rescheduledBy->full_name : 'System',
        ]);

        // TODO: Implement WhatsApp notification
        // $this->sendWhatsAppNotification($patient->phone, [
        //     'template' => 'appointment_rescheduled',
        //     'appointment_number' => $appointment->appointment_number,
        //     'old_date' => $event->oldData['date'],
        //     'old_time' => $event->oldData['time'],
        //     'new_date' => $event->newData['date'],
        //     'new_time' => $event->newData['time'],
        // ]);
    }

    /**
     * Placeholder for WhatsApp notification integration.
     *
     * TODO: Integrate with WhatsApp Business API
     * - Twilio WhatsApp API
     * - Facebook WhatsApp Business API
     * - Third-party service (e.g., MessageBird, Vonage)
     */
    private function sendWhatsAppNotification(string $phone, array $data): void
    {
        // Placeholder for future WhatsApp integration
        Log::info('WhatsApp Notification Placeholder', [
            'phone' => $phone,
            'data' => $data,
        ]);

        // Example implementation (commented out):
        /*
        $twilio = new \Twilio\Rest\Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );

        $twilio->messages->create(
            "whatsapp:{$phone}",
            [
                'from' => "whatsapp:" . config('services.twilio.whatsapp_number'),
                'body' => $this->formatWhatsAppMessage($data),
            ]
        );
        */
    }

    /**
     * Format WhatsApp message based on template.
     */
    private function formatWhatsAppMessage(array $data): string
    {
        // This will be customized based on your WhatsApp template
        return "Your appointment notification...";
    }
}
