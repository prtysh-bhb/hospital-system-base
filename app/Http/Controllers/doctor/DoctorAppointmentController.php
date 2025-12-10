<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Doctor\DoctorAppointmentServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DoctorAppointmentController extends Controller
{
    public function index()
    {
        return view('doctor.appointments');
    }

    /**
     * AJAX endpoint returning doctor's appointments (JSON)
     */
    public function doctorAppointmentData(Request $request, DoctorAppointmentServices $svc)
    {
        $doctorId = Auth::id();
        $filters = [];
        // allow optional date (Y-m-d), status, search
        if ($request->has('date') && ! empty($request->get('date'))) {
            // validate date format loosely
            $filters['date'] = Carbon::parse($request->get('date'))->toDateString();
        }
        if ($request->has('status')) {
            $filters['status'] = $request->get('status');
        }
        if ($request->has('search')) {
            $filters['search'] = $request->get('search');
        }

        $perPage = 5;

        $paginated = $svc->getTodayAppointments($doctorId, $filters)
            ->paginate($perPage);

        $paginated->getCollection()->transform(function ($a) {
            $patient = $a->patient;
            $profile = optional($patient)->patientProfile;
            // compute age if date_of_birth exists
            $age = null;
            if (! empty(optional($patient)->date_of_birth)) {
                try {
                    $age = Carbon::parse($patient->date_of_birth)->age;
                } catch (\Exception $e) {
                    $age = null;
                }
            }

            return [
                'id' => $a->id,
                'appointment_number' => $a->appointment_number,
                'patient_id' => optional($patient)->id,
                'patient_name' => trim(optional($patient)->first_name.' '.optional($patient)->last_name),
                'patient_age' => $age,
                'patient_gender' => optional($patient)->gender,
                'patient_phone' => optional($patient)->phone,
                'patient_allergies' => optional($profile)->allergies,
                'reason' => $a->reason_for_visit,
                'date' => optional($a->appointment_date) ? optional($a->appointment_date)->format('d-m-Y') : '',
                'time' => optional($a->appointment_time) ? Carbon::parse($a->appointment_time)->format('h:i A') : '',
                'status' => $a->status,
                'details_url' => route('doctor.appointment-details', ['id' => $a->id]),
            ];
        });

        return response()->json($paginated);
    }

    /**
     * Mark an appointment as completed (AJAX).
     */
    public function completeAppointment(Request $request, $id)
    {
        try {
            $doctorId = Auth::id();

            // Fetch appointment belonging to this doctor
            $appointment = Appointment::where('id', $id)
                ->where('doctor_id', $doctorId)
                ->first();

            if (! $appointment) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found or unauthorized',
                ], 404);
            }

            // Mark appointment as completed
            $appointment->status = 'completed';
            $appointment->save();

            return response()->json([
                'status' => 200,
                'msg' => 'Appointment marked as completed',
                'id' => $appointment->id,
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 400);

        }
    }

    public function doctorAppointmentDetails($id, DoctorAppointmentServices $svc)
    {
        $doctorId = Auth::id();
        $appointment = $svc->getAppointmentDetails($id, $doctorId);

        if (! $appointment) {
            abort(404, 'Appointment not found');
        }

        return view('doctor.appointment-details', compact('appointment'));
    }

    /**
     * Get appointment details as JSON (AJAX).
     */
    public function getAppointmentDetailsJson($id, DoctorAppointmentServices $svc)
    {
        try {
            $doctorId = Auth::id();
            $appointment = $svc->getAppointmentDetails($id, $doctorId);

            if (! $appointment) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found',
                ], 404);
            }

            $patient = $appointment->patient;
            $profile = optional($patient)->patientProfile;

            // Calculate age
            $age = null;
            if (! empty($patient->date_of_birth)) {
                try {
                    $age = Carbon::parse($patient->date_of_birth)->age;
                } catch (\Exception $e) {
                    $age = null;
                }
            }

            // Parse allergies and medications
            $allergies = [];
            if ($profile && $profile->allergies) {
                $allergies = array_filter(array_map('trim', explode(',', $profile->allergies)));
            }

            $medications = [];
            if ($profile && $profile->current_medications) {
                $medications = array_filter(array_map('trim', explode(',', $profile->current_medications)));
            }

            $medicalHistory = [];
            if ($profile && $profile->medical_history) {
                $medicalHistory = array_filter(array_map('trim', explode(',', $profile->medical_history)));
            }

            // Get previous appointments
            $previousAppointments = Appointment::where('patient_id', $patient->id)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'completed')
                ->with('doctor.doctorProfile.specialty')
                ->orderBy('appointment_date', 'desc')
                ->take(5)
                ->get()
                ->map(function ($apt) {
                    $doctor = $apt->doctor;
                    $doctorName = trim(optional($doctor)->first_name.' '.optional($doctor)->last_name);
                    $specialty = optional(optional($doctor->doctorProfile)->specialty)->name ?? 'General';

                    return [
                        'date' => optional($apt->appointment_date)->format('F d, Y'),
                        'reason' => $apt->reason_for_visit ?? 'General Checkup',
                        'doctor' => $doctorName,
                        'specialty' => $specialty,
                    ];
                });

            // Get prescriptions
            $prescriptions = $appointment->prescriptions->map(function ($presc) {
                return [
                    'id' => $presc->id,
                    'prescription_number' => $presc->prescription_number,
                    'diagnosis' => $presc->diagnosis,
                    'medications' => $presc->medications ?? [],
                    'instructions' => $presc->instructions,
                    'notes' => $presc->notes,
                    'created_at' => optional($presc->created_at)->format('F d, Y h:i A'),
                ];
            });

            $data = [
                'appointment' => [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'date' => optional($appointment->appointment_date)->format('F d, Y'),
                    'time' => Carbon::parse($appointment->appointment_time)->format('h:i A'),
                    'type' => ucfirst($appointment->appointment_type ?? 'Consultation'),
                    'status' => $appointment->status,
                    'reason' => $appointment->reason_for_visit,
                    'symptoms' => $appointment->symptoms,
                    'notes' => $appointment->notes,
                    'duration' => $appointment->duration_minutes ?? 30,
                ],
                'patient' => [
                    'id' => $patient->id,
                    'patient_number' => 'PT-'.date('Y').'-'.str_pad($patient->id, 3, '0', STR_PAD_LEFT),
                    'name' => trim($patient->first_name.' '.$patient->last_name),
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'age' => $age,
                    'gender' => ucfirst($patient->gender ?? 'Unknown'),
                    'date_of_birth' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->format('F d, Y') : null,
                    'address' => $patient->address,
                    'profile_image' => $patient->profile_image,
                    'blood_group' => optional($profile)->blood_group,
                    'emergency_contact_name' => optional($profile)->emergency_contact_name,
                    'emergency_contact_phone' => optional($profile)->emergency_contact_phone,
                    'allergies' => $allergies,
                    'medical_history' => $medicalHistory,
                    'current_medications' => $medications,
                    'previous_appointments' => $previousAppointments,
                ],
                'prescriptions' => $prescriptions,
            ];

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save consultation notes (AJAX).
     */
    public function saveConsultationNotes(Request $request, $id, DoctorAppointmentServices $svc)
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:5000',
            ]);

            $doctorId = Auth::id();
            $success = $svc->saveConsultationNotes($id, $doctorId, $request->notes);

            if (! $success) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'msg' => 'Consultation notes saved successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save or update prescription (AJAX).
     */
    public function savePrescription(Request $request, $id, DoctorAppointmentServices $svc)
    {
        try {
            $request->validate([
                'diagnosis' => 'nullable|string|max:1000',
                'medications' => 'required|array',
                'medications.*.name' => 'required|string',
                'medications.*.dosage' => 'required|string',
                'medications.*.frequency' => 'required|string',
                'medications.*.duration' => 'required|string',
                'medications.*.quantity' => 'nullable|string',
                'instructions' => 'nullable|string|max:2000',
                'notes' => 'nullable|string|max:2000',
            ]);

            $doctorId = Auth::id();
            $prescription = $svc->savePrescription($id, $doctorId, $request->all());

            if (! $prescription) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'msg' => 'Prescription saved successfully',
                'prescription' => [
                    'id' => $prescription->id,
                    'prescription_number' => $prescription->prescription_number,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save vital signs (AJAX).
     */
    public function saveVitalSigns(Request $request, $id, DoctorAppointmentServices $svc)
    {
        try {
            $request->validate([
                'blood_pressure' => 'nullable|string|max:20',
                'heart_rate' => 'nullable|string|max:20',
                'temperature' => 'nullable|string|max:20',
                'oxygen_saturation' => 'nullable|string|max:20',
                'weight' => 'nullable|string|max:20',
                'height' => 'nullable|string|max:20',
            ]);

            $doctorId = Auth::id();

            // Build vital signs data
            $vitalsData = [];
            if ($request->blood_pressure) {
                $vitalsData['blood_pressure'] = $request->blood_pressure;
            }
            if ($request->heart_rate) {
                $vitalsData['heart_rate'] = $request->heart_rate;
            }
            if ($request->temperature) {
                $vitalsData['temperature'] = $request->temperature;
            }
            if ($request->oxygen_saturation) {
                $vitalsData['oxygen_saturation'] = $request->oxygen_saturation;
            }
            if ($request->weight) {
                $vitalsData['weight'] = $request->weight;
            }
            if ($request->height) {
                $vitalsData['height'] = $request->height;
            }

            if (empty($vitalsData)) {
                return response()->json([
                    'status' => 400,
                    'msg' => 'Please provide at least one vital sign',
                ], 400);
            }

            // Add timestamp to vitals
            $vitalsData['recorded_at'] = now()->format('F d, Y h:i A');
            $vitalsData['type'] = 'vital_signs';

            $success = $svc->saveVitalSigns($id, $doctorId, $vitalsData);

            if (! $success) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'msg' => 'Vital signs saved successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available time slots for a specific date (AJAX).
     */
    public function getAvailableSlots(Request $request, DoctorAppointmentServices $svc)
    {
        try {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
            ]);

            $doctorId = Auth::id();
            $result = $svc->getAvailableSlots($doctorId, $request->date);

            return response()->json([
                'status' => 200,
                'data' => $result,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Schedule follow-up appointment (AJAX).
     */
    public function scheduleFollowUp(Request $request, $id, DoctorAppointmentServices $svc)
    {
        try {
            $request->validate([
                'appointment_date' => 'required|date|after_or_equal:tomorrow',
                'appointment_time' => 'required',
                'reason' => 'nullable|string|max:500',
                'duration_minutes' => 'nullable|integer|min:15|max:180',
            ]);

            $doctorId = Auth::id();
            $followUp = $svc->scheduleFollowUp($id, $doctorId, $request->all());

            // Check if error was returned
            if (is_array($followUp) && isset($followUp['error'])) {
                return response()->json([
                    'status' => 400,
                    'msg' => $followUp['error'],
                ], 400);
            }

            if (! $followUp) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Original appointment not found',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'msg' => 'Follow-up appointment scheduled successfully',
                'appointment' => [
                    'id' => $followUp->id,
                    'appointment_number' => $followUp->appointment_number,
                    'date' => optional($followUp->appointment_date)->format('F d, Y'),
                    'time' => Carbon::parse($followUp->appointment_time)->format('h:i A'),
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
