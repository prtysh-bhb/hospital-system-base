<?php

namespace App\Services\Doctor;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\AppointmentSlotService;

class DoctorAppointmentServices
{
    protected $slotService;

    /**
     * Create a new class instance.
     */
    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Return today's appointments for a doctor with optional filters.
     *
     * @param  array  $filters  ['status' => string, 'search' => string, 'date' => Y-m-d]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTodayAppointments(int $doctorId, array $filters = [])
    {
        $query = Appointment::with(['patient.patientProfile'])
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $filters['date'] ?? now()->toDateString())
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                })->orWhere('appointment_number', 'like', "%{$search}%");
            });
        }

        // Return the query builder so callers can paginate
        return $query;
    }

    /**
     * Get detailed appointment information with all related data.
     *
     * @return Appointment|null
     */
    public function getAppointmentDetails(int $appointmentId, int $doctorId)
    {
        return Appointment::with([
            'patient.patientProfile',
            'prescriptions' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ])
            ->where('id', $appointmentId)
            ->where('doctor_id', $doctorId)
            ->first();
    }

    /**
     * Save consultation notes for an appointment.
     *
     * @return bool
     */
    public function saveConsultationNotes(int $appointmentId, int $doctorId, string $notes)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('doctor_id', $doctorId)
            ->first();

        if (! $appointment) {
            return false;
        }

        $appointment->notes = $notes;

        return $appointment->save();
    }

    /**
     * Save vital signs to prescription medications field.
     *
     * @return bool
     */
    public function saveVitalSigns(int $appointmentId, int $doctorId, array $vitalsData)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('doctor_id', $doctorId)
            ->first();

        if (! $appointment) {
            \Log::error('Appointment not found for vital signs', ['appointment_id' => $appointmentId, 'doctor_id' => $doctorId]);

            return false;
        }

        try {
            // Get or create prescription for this appointment
            $prescription = Prescription::where('appointment_id', $appointmentId)->first();

            if (! $prescription) {
                // Create new prescription with vital signs
                \Log::info('Creating new prescription with vital signs', ['appointment_id' => $appointmentId]);
                $prescription = Prescription::create([
                    'prescription_number' => 'RX-'.date('Y').'-'.str_pad(Prescription::count() + 1, 6, '0', STR_PAD_LEFT),
                    'appointment_id' => $appointmentId,
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $doctorId,
                    'medications' => [$vitalsData],
                ]);
                \Log::info('Prescription created successfully', ['prescription_id' => $prescription->id]);
            } else {
                // Append vital signs to existing medications array
                \Log::info('Appending vital signs to existing prescription', ['prescription_id' => $prescription->id]);
                $medications = $prescription->medications ?? [];
                $medications[] = $vitalsData;
                $prescription->medications = $medications;
                $saved = $prescription->save();
                \Log::info('Vital signs appended', ['saved' => $saved, 'medications_count' => count($medications)]);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to save vital signs', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointmentId,
                'vitals_data' => $vitalsData,
            ]);

            return false;
        }
    }

    /**
     * Create or update prescription for an appointment.
     *
     * @return Prescription|null
     */
    public function savePrescription(int $appointmentId, int $doctorId, array $prescriptionData)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('doctor_id', $doctorId)
            ->first();

        if (! $appointment) {
            return null;
        }

        // Check if prescription exists
        $prescription = Prescription::where('appointment_id', $appointmentId)->first();

        $data = [
            'appointment_id' => $appointmentId,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctorId,
            'diagnosis' => $prescriptionData['diagnosis'] ?? null,
            'medications' => $prescriptionData['medications'] ?? [],
            'instructions' => $prescriptionData['instructions'] ?? null,
            'follow_up_date' => $prescriptionData['follow_up_date'] ?? null,
            'notes' => $prescriptionData['notes'] ?? null,
        ];

        if ($prescription) {
            $prescription->update($data);

            return $prescription;
        }

        // Generate prescription number
        $data['prescription_number'] = 'RX-'.date('Y').'-'.str_pad(Prescription::count() + 1, 6, '0', STR_PAD_LEFT);

        return Prescription::create($data);
    }

    /**
     * Schedule a follow-up appointment.
     *
     * @return Appointment|array
     */
    public function scheduleFollowUp(int $originalAppointmentId, int $doctorId, array $followUpData)
    {
        $originalAppointment = Appointment::where('id', $originalAppointmentId)
            ->where('doctor_id', $doctorId)
            ->first();

        if (! $originalAppointment) {
            return ['error' => 'Original appointment not found'];
        }

        // Validate time slot availability
        $validation = $this->slotService->validateAppointmentTime(
            $doctorId,
            $followUpData['appointment_date'],
            $followUpData['appointment_time']
        );

        if (! $validation['valid']) {
            return ['error' => $validation['message']];
        }

        // Generate appointment number
        $appointmentNumber = 'APT-'.date('Y').'-'.str_pad(Appointment::count() + 1, 6, '0', STR_PAD_LEFT);

        // Convert time to 24-hour format if needed
        $appointmentTime = $followUpData['appointment_time'];
        if (preg_match('/(AM|PM|am|pm)/', $appointmentTime)) {
            $appointmentTime = date('H:i:s', strtotime($appointmentTime));
        } elseif (substr_count($appointmentTime, ':') == 1) {
            $appointmentTime .= ':00';
        }

        $data = [
            'appointment_number' => $appointmentNumber,
            'patient_id' => $originalAppointment->patient_id,
            'doctor_id' => $doctorId,
            'appointment_date' => $followUpData['appointment_date'],
            'appointment_time' => $appointmentTime,
            'duration_minutes' => $followUpData['duration_minutes'] ?? 30,
            'status' => 'confirmed',
            'appointment_type' => 'follow_up',
            'reason_for_visit' => $followUpData['reason'] ?? 'Follow-up appointment',
            'booked_by' => $doctorId,
            'booked_via' => 'phone',
        ];

        try {
            $appointment = Appointment::create($data);

            return $appointment;
        } catch (\Exception $e) {
            \Log::error('Failed to create follow-up appointment: '.$e->getMessage());

            return ['error' => 'Failed to create appointment: '.$e->getMessage()];
        }
    }

    /**
     * Get available time slots for a doctor on a specific date.
     *
     * @return array
     */
    public function getAvailableSlots(int $doctorId, string $date)
    {
        return $this->slotService->getAvailableSlots($doctorId, $date);
    }
}
