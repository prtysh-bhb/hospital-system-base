<?php

namespace App\Services\Public;

use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\Setting;
use App\Models\User;
use App\Services\AppointmentSlotService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BookAppointmentService
{
    protected $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Get form field visibility settings for patient forms
     * Centralized method used across all appointment creation interfaces
     */
    public static function getFormSettings()
    {
        return [
            'show_emergency_contact' => Setting::getValue('show_emergency_contact', '0') == '1',
            'show_blood_group' => Setting::getValue('show_blood_group', '0') == '1',
            'show_medical_history' => Setting::getValue('show_medical_history', '0') == '1',
            'show_current_medications' => Setting::getValue('show_current_medications', '0') == '1',
            'show_insurance_details' => Setting::getValue('show_insurance_details', '0') == '1',
        ];
    }

    public function createAppointment(array $data)
    {
        try {
            DB::beginTransaction();

            // Validate if the time slot is available before creating appointment
            $slotValidation = $this->slotService->validateAppointmentTime(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time']
            );

            if (! $slotValidation['valid']) {
                return [
                    'success' => false,
                    'message' => $slotValidation['message'],
                ];
            }

            // Check if user exists by email or phone
            $user = User::where('email', $data['email'])
                ->orWhere('phone', $data['phone'])
                ->first();

            if (! $user) {
                // Create new user
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['phone']), // Password is phone number
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'address' => $data['address'] ?? null,
                    'role' => 'patient',
                    'status' => 'active',
                ]);

                // Create patient profile with optional fields
                PatientProfile::create([
                    'user_id' => $user->id,
                    'allergies' => $data['allergies'] ?? null,
                    'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                    'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                    'blood_group' => $data['blood_group'] ?? null,
                    'medical_history' => $data['medical_history'] ?? null,
                    'current_medications' => $data['current_medications'] ?? null,
                    'insurance_provider' => $data['insurance_provider'] ?? null,
                    'insurance_number' => $data['insurance_number'] ?? null,
                ]);
            } else {
                // Update existing user info if needed
                $user->update([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'address' => $data['address'] ?? $user->address,
                ]);

                // Update patient profile if exists, or create if not
                if ($user->patientProfile) {
                    $user->patientProfile->update([
                        'allergies' => $data['allergies'] ?? $user->patientProfile->allergies,
                        'emergency_contact_name' => $data['emergency_contact_name'] ?? $user->patientProfile->emergency_contact_name,
                        'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $user->patientProfile->emergency_contact_phone,
                        'blood_group' => $data['blood_group'] ?? $user->patientProfile->blood_group,
                        'medical_history' => $data['medical_history'] ?? $user->patientProfile->medical_history,
                        'current_medications' => $data['current_medications'] ?? $user->patientProfile->current_medications,
                        'insurance_provider' => $data['insurance_provider'] ?? $user->patientProfile->insurance_provider,
                        'insurance_number' => $data['insurance_number'] ?? $user->patientProfile->insurance_number,
                    ]);
                } else {
                    // Create patient profile if user exists but doesn't have one
                    PatientProfile::create([
                        'user_id' => $user->id,
                        'allergies' => $data['allergies'] ?? null,
                        'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                        'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                        'blood_group' => $data['blood_group'] ?? null,
                        'medical_history' => $data['medical_history'] ?? null,
                        'current_medications' => $data['current_medications'] ?? null,
                        'insurance_provider' => $data['insurance_provider'] ?? null,
                        'insurance_number' => $data['insurance_number'] ?? null,
                    ]);
                }
            }

            // Generate unique appointment number
            $date = now()->format('Ymd');
            $random = random_int(0, 999999);
            $randomPadded = str_pad($random, 6, '0', STR_PAD_LEFT);
            $appointmentNumber = 'APT-'.$date.'-'.$randomPadded;
            // $appointmentNumber = 'APT-'.date('Y').'-'.str_pad(Appointment::count() + 1, 6, '0', STR_PAD_LEFT);

            // Parse appointment time (format: "09:00 AM")
            $appointmentDateTime = Carbon::parse($data['appointment_date'].' '.$data['appointment_time']);

            // Create appointment
            $appointment = Appointment::create([
                'appointment_number' => $appointmentNumber,
                'patient_id' => $user->id,
                'doctor_id' => $data['doctor_id'],
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $appointmentDateTime->format('H:i:s'),
                'duration_minutes' => 30, // Default duration
                'status' => 'pending',
                'appointment_type' => $data['appointment_type'] ?? 'consultation',
                'reason_for_visit' => $data['reason_for_visit'],
                'symptoms' => $data['allergies'] ?? null,
                'notes' => $data['notes'] ?? null,
                'booked_via' => $data['booked_via'] ?? 'online',
                'reminder_sent' => false,
            ]);

            DB::commit();

            \Log::info('Appointment created successfully', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'appointment_number' => $appointmentNumber,
            ]);

            return [
                'success' => true,
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointmentNumber,
                'user' => $user,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating appointment: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getAppointmentDetails($appointmentId)
    {
        return Appointment::with(['patient', 'doctor.doctorProfile.specialty'])->findOrFail($appointmentId);
    }
}
