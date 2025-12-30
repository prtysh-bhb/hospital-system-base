<?php

namespace App\Http\Controllers\Patient;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Str;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Models\PatientProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Events\NotifiyUserEvent;
use App\Enums\WhatsappTemplating;
use App\Models\AppointmentHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\AppointmentSlotService;
use App\Services\Public\BookAppointmentService;

class DashboardController extends Controller
{
    protected $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    public function index()
    {
        $user = auth()->user();
        $doctors = User::where('role', 'doctor')->with('doctorProfile.specialty')->get();
        // Get appointments with all related data
        $appointments = $user->patientAppointments()
            ->with([
                'doctor.doctorProfile.specialty',
                'prescriptions',
            ])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                // Get doctor info
                $doctor = $appointment->doctor;
                $doctorProfile = $doctor->doctorProfile ?? null;
                $specialty = $doctorProfile?->specialty;

                // Get prescription if exists
                $prescription = $appointment->prescriptions->first();

                return (object) [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'doctor_id' => $doctor->id,
                    'doctor_name' => 'Dr. '.($doctor->full_name ?? 'Unknown'),
                    'doctor_image' => $doctor->profile_image,
                    'specialty' => $specialty?->name ?? 'General Medicine',
                    'specialty_id' => $specialty?->id,
                    'qualification' => $doctorProfile?->qualification ?? '',
                    'consultation_fee' => $doctorProfile?->consultation_fee ?? 0,
                    'date' => $appointment->formatted_date,
                    'time' => $appointment->formatted_time,
                    'date_raw' => $appointment->appointment_date,
                    'duration' => $appointment->duration_minutes ?? 30,
                    'status' => $appointment->status ? ucwords(str_replace('_', ' ', $appointment->status)) : 'Pending',
                    'appointment_type' => $appointment->appointment_type ? ucwords(str_replace('_', ' ', $appointment->appointment_type)) : 'Consultation',
                    'reason_for_visit' => $appointment->reason_for_visit,
                    'symptoms' => $appointment->symptoms,
                    'notes' => $appointment->notes,
                    'cancellation_reason' => $appointment->cancellation_reason,

                    // Prescription data
                    'has_prescription' => $prescription !== null,
                    'prescription' => $prescription ? (object) [
                        'id' => $prescription->id,
                        'prescription_number' => $prescription->prescription_number,
                        'diagnosis' => $prescription->diagnosis,
                        'medications' => $prescription->medications ?? [],
                        'instructions' => $prescription->instructions,
                        'follow_up_date' => $prescription->follow_up_date ? Carbon::parse($prescription->follow_up_date)->format('F j, Y') : null,
                        'notes' => $prescription->notes,
                    ] : null,
                ];
            });

        // Calculate stats
        $today = Carbon::today();

        $stats = (object) [
            'total' => $appointments->count(),
            'today' => $appointments->filter(fn($a) => $a->date_raw->isSameDay($today))->count(),
            'upcoming' => $appointments->filter(fn($a) =>$a->date_raw->gte($today) && in_array($a->status, ['pending', 'confirmed']))->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        return view('patient.dashboard', compact('appointments', 'stats', 'doctors'));
    }

    public function cancelAppointment(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                'cancellation_reason' => 'required|string|min:10|max:500',
            ], [
                'cancellation_reason.required' => 'Please provide a reason for cancellation.',
                'cancellation_reason.min' => 'Cancellation reason must be at least 10 characters.',
                'cancellation_reason.max' => 'Cancellation reason cannot exceed 500 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $appointment = Appointment::findOrFail($request->appointment_id);

            // Verify the appointment belongs to the logged-in patient
            if ($appointment->patient_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to cancel this appointment.',
                ], 403);
            }

            // Check if appointment can be cancelled
            if (! in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending or confirmed appointments can be cancelled.',
                ], 400);
            }

            // Update appointment status
            $appointment->status = 'cancelled';
            $appointment->cancellation_reason = $request->cancellation_reason;
            $appointment->cancelled_at = now();
            $appointment->save();

            // Send WhatsApp notification
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;
            if ($patient->phone) {
                $appointmentDate = Carbon::parse($appointment->appointment_date)->format('F jS');
                $appointmentTime = Carbon::parse($appointment->appointment_time)->format('g:i A');

                $doctorName = 'Dr. ' . trim($doctor->first_name . ' ' . $doctor->last_name);
                $patientName = trim($patient->first_name . ' ' . $patient->last_name);
                $status = ucfirst($appointment->status);

                $components = [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['key' => 'name', 'type' => 'text', 'text' => $patientName],
                            ['key' => 'doctor_name', 'type' => 'text', 'text' => $doctorName],
                            ['key' => 'date', 'type' => 'text', 'text' => $appointmentDate],
                            ['key' => 'time', 'type' => 'text', 'text' => $appointmentTime],
                            ['key' => 'cancellation_reason', 'type' => 'text', 'text' => $request->cancellation_reason],
                        ],
                    ],
                ];

                $params = [
                    'phone_number' => $patient->phone,
                    'template_name' => WhatsappTemplating::CANCEL_APPOINTMENT->value,
                    'components' => $components,
                    'appointment_data' => [
                        'appointment_id' => $appointment->id,
                        'appointment_number' => $appointment->appointment_number,
                        'patient_name' => $patientName,
                        'doctor_name' => $doctorName,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'appointment_status' => $status,
                    ],
                ];

                event(new NotifiyUserEvent($params));
            }


            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully!',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Cancel appointment error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling the appointment. Please try again.',
            ], 500);
        }
    }

    public function rescheduleAppointment(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                'new_date' => 'required|date|after_or_equal:today',
                'new_time' => 'required|date_format:H:i',
            ], [
                'new_date.required' => 'Please select a new appointment date.',
                'new_date.after_or_equal' => 'Appointment date must be today or a future date.',
                'new_time.required' => 'Please select a new appointment time.',
                'new_time.date_format' => 'Invalid time format.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $appointment = Appointment::findOrFail($request->appointment_id);

            // Verify the appointment belongs to the logged-in patient
            if ($appointment->patient_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to reschedule this appointment.',
                ], 403);
            }

            // Check if appointment can be rescheduled
            if (! in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending or confirmed appointments can be rescheduled.',
                ], 400);
            }

            // Check if the new date/time is not in the past
            $newDateTime = Carbon::parse($request->new_date.' '.$request->new_time);
            if ($newDateTime->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reschedule to a past date and time.',
                ], 400);
            }

            // Check if doctor is available at the new time slot
            $conflictingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('id', '!=', $appointment->id)
                ->where('appointment_date', $request->new_date)
                ->where('appointment_time', $request->new_time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($conflictingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected time slot is not available. Please choose another time.',
                ], 400);
            }

            // Store old appointment details for response
            $oldDate = $appointment->appointment_date;
            $oldTime = $appointment->appointment_time;

            // Update appointment with new date and time only
            $appointment->appointment_date = $request->new_date;
            $appointment->appointment_time = $request->new_time;
            $appointment->save();

            // Send WhatsApp cancellation notification via NotifyUserEvent
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;

            if ($patient->phone) {
                $appointmentDate = Carbon::parse($appointment->appointment_date)->format('F jS');
                $appointmentTime = Carbon::parse($appointment->appointment_time)->format('g:i A');

                $doctorName = 'Dr. ' . trim($doctor->first_name . ' ' . $doctor->last_name);
                $patientName = trim($patient->first_name . ' ' . $patient->last_name);
                $status = ucfirst($appointment->status);

                $components = [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['key' => 'name', 'type' => 'text', 'text' => $patientName],
                            ['key' => 'date', 'type' => 'text', 'text' => $appointmentDate],
                            ['key' => 'time', 'type' => 'text', 'text' => $appointmentTime],
                            
                        ],
                    ],
                ];

                $params = [
                    'phone_number' => $patient->phone,
                    'template_name' => WhatsappTemplating::RESCHEDULE_APPOINTMENT->value,
                    'components' => $components,
                    'appointment_data' => [
                        'appointment_id' => $appointment->id,
                        'appointment_number' => $appointment->appointment_number,
                        'patient_name' => $patientName,
                        'doctor_name' => $doctorName,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'appointment_status' => $status,
                    ],
                ];

                event(new NotifiyUserEvent($params));
            }

            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully!',
                'data' => [
                    'old_date' => Carbon::parse($oldDate)->format('F j, Y'),
                    'old_time' => Carbon::parse($oldTime)->format('g:i A'),
                    'new_date' => Carbon::parse($request->new_date)->format('F j, Y'),
                    'new_time' => Carbon::parse($request->new_date.' '.$request->new_time)->format('g:i A'),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Reschedule appointment error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rescheduling the appointment. Please try again.',
            ], 500);
        }
    }

    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'doctor_id' => 'required|exists:users,id',
                'date' => 'required|date|after_or_equal:today',
                'appointment_id' => 'nullable|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $date = Carbon::parse($request->date);
            $doctorId = $request->doctor_id;
            $appointmentId = $request->appointment_id;

            // Get available time slots using the slot service
            $result = $this->slotService->getAvailableSlots($doctorId, $date, $appointmentId);

            // Format slots for frontend consumption
            $formattedSlots = [];
            if (isset($result['slots']) && is_array($result['slots'])) {
                foreach ($result['slots'] as $slot) {
                    // Convert 12-hour format to 24-hour format for value
                    $time24 = Carbon::parse($slot)->format('H:i');
                    $formattedSlots[] = [
                        'time' => $time24,
                        'formatted_time' => $slot,
                    ];
                }
            }

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? '',
                'slots' => $formattedSlots,
                'on_leave' => $result['on_leave'] ?? false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get time slots error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching time slots.',
            ], 500);
        }
    }

    public function getMedicalHistory(Request $request)
    {
        try {
            $user = auth()->user();

            // Get appointment history from appointment_histories table
            $histories = AppointmentHistory::with([
                'appointment.doctor.doctorProfile.specialty',
                'appointment.prescriptions',
            ])
                ->whereHas('appointment', function ($query) use ($user) {
                    $query->where('patient_id', $user->id);
                })
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get()
                ->map(function ($history) {
                    $appointment = $history->appointment;
                    $doctor = $appointment->doctor;
                    $doctorProfile = $doctor->doctorProfile ?? null;
                    $specialty = $doctorProfile?->specialty;
                    $prescription = $appointment->prescriptions->first();

                    return [
                        'id' => $appointment->id,
                        'appointment_number' => $appointment->appointment_number,
                        'doctor_name' => 'Dr. '.($doctor->full_name ?? 'Unknown'),
                        'doctor_image' => $doctor->profile_image,
                        'specialty' => $specialty?->name ?? 'General Medicine',
                        'qualification' => $doctorProfile?->qualification ?? '',
                        'date' => Carbon::parse($history->appointment_date)->format('M j, Y'),
                        'time' => Carbon::parse($history->appointment_time)->format('g:i A'),
                        'status' => $history->status,
                        'note' => $history->note,
                        'has_prescription' => $prescription !== null,
                        'prescription' => $prescription ? [
                            'id' => $prescription->id,
                            'prescription_number' => $prescription->prescription_number,
                            'diagnosis' => $prescription->diagnosis,
                            'medications' => $prescription->medications ?? [],
                            'instructions' => $prescription->instructions,
                            'follow_up_date' => $prescription->follow_up_date ? Carbon::parse($prescription->follow_up_date)->format('F j, Y') : null,
                            'notes' => $prescription->notes,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'histories' => $histories,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get medical history error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching medical history.',
            ], 500);
        }
    }

    public function downloadPrescription($id)
    {
        try {
            $user = auth()->user();

            // Find prescription and verify it belongs to the patient
            $prescription = Prescription::with([
                'appointment.patient',
                'appointment.doctor.doctorProfile.specialty',
            ])->findOrFail($id);

            // Verify the prescription belongs to the logged-in patient
            if ($prescription->appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access to prescription.');
            }

            $appointment = $prescription->appointment;
            $doctor = $appointment->doctor;
            $doctorProfile = $doctor->doctorProfile;
            $patient = $appointment->patient;

            // Prepare data for PDF
            $data = [
                'prescription' => $prescription,
                'appointment' => $appointment,
                'doctor' => $doctor,
                'doctorProfile' => $doctorProfile,
                'patient' => $patient,
                'date' => Carbon::parse($prescription->created_at)->format('F j, Y'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('prescriptions.pdf', $data);

            // Download PDF
            return $pdf->download('prescription-'.$prescription->prescription_number.'.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Prescription not found.');
        } catch (\Exception $e) {
            \Log::error('Download prescription error: '.$e->getMessage());

            return back()->with('error', 'An error occurred while downloading the prescription.');
        }
    }

    public function storeAppointment(Request $request)
    {
        try {
            // Determine whether a patient was selected or new patient details are provided
            $patientId = $request->input('patient_id');

            if ($patientId && $patientId != '') {
                // Validate required fields when patient selected
                $request->validate([
                    'patient_id' => [
                        'required',
                        Rule::exists('users', 'id')->where(function ($query) {
                            $query->where('role', 'patient');
                        })
                    ],
                    'doctor_id' => [
                        'required',
                        Rule::exists('users', 'id')->where(function ($query) {
                            $query->where('role', 'doctor');
                        })
                    ],
                    'appointment_date' => 'required|date|after_or_equal:today',
                    'appointment_time' => 'required',
                    'appointment_type' => 'required|in:consultation,follow_up,emergency,check_up',
                    'reason_for_visit' => 'required|string|max:1000',
                ], [
                    'patient_id.required' => 'Please select a patient.',
                    'doctor_id.required' => 'Please select a doctor.',
                    'appointment_date.required' => 'Appointment date is required.',
                    'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
                    'appointment_time.required' => 'Appointment time is required.',
                    'appointment_type.required' => 'Please select appointment type.',
                    'reason_for_visit.required' => 'Reason for visit is required.',
                ]);
            } else {
                // Validate patient details + appointment when creating new patient
                $request->validate([
                    'doctor_id' => [
                        'required',
                        Rule::exists('users', 'id')->where(function ($query) {
                            $query->where('role', 'doctor');
                        })
                    ],
                    'appointment_date' => 'required|date|after_or_equal:today',
                    'appointment_time' => 'required',
                    'appointment_type' => 'required|in:consultation,follow_up,emergency,check_up',
                    'reason_for_visit' => 'required|string|max:1000',
                    'notes' => 'nullable|string|max:500',
                ], [
                    'doctor_id.required' => 'Please select a doctor.',
                    'appointment_date.required' => 'Appointment date is required.',
                    'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
                    'appointment_time.required' => 'Appointment time is required.',
                    'appointment_type.required' => 'Please select appointment type.',
                    'reason_for_visit.required' => 'Reason for visit is required.',
                    'reason_for_visit.max' => 'Reason for visit cannot exceed 1000 characters.',
                    'notes.max' => 'Notes cannot exceed 500 characters.',
                ]);
            }

            $date = now()->format('Ymd');
            $random = random_int(0, 999999);
            $randomPadded = str_pad($random, 6, '0', STR_PAD_LEFT);
            $appointmentNumber = 'APT-' . $date . '-' . $randomPadded;

            // Parse appointment time (could be "09:00 AM" format or "09:00" format)
            $appointmentTime = $request->input('appointment_time');

            // Validate if the time slot is available
            $slotValidation = $this->slotService->validateAppointmentTime(
                $request->input('doctor_id'),
                $request->input('appointment_date'),
                $appointmentTime
            );

            if (!$slotValidation['valid']) {
                return response()->json([
                    'status' => 422,
                    'msg' => $slotValidation['message'],
                    'errors' => ['appointment_time' => [$slotValidation['message']]],
                ], 422);
            }

            // Convert 12-hour format to 24-hour if needed
            if (strpos($appointmentTime, 'AM') !== false || strpos($appointmentTime, 'PM') !== false) {
                $appointmentTime = date('H:i:s', strtotime($appointmentTime));
            } else {
                // Ensure it has seconds
                if (substr_count($appointmentTime, ':') == 1) {
                    $appointmentTime .= ':00';
                }
            }

            // Create a new appointment instance
            $appointment = new Appointment;
            $appointment->appointment_number = $appointmentNumber;
            $appointment->patient_id = $patientId;
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->appointment_date = $request->input('appointment_date');
            $appointment->appointment_time = $appointmentTime;
            $appointment->appointment_type = $request->input('appointment_type');
            $appointment->reason_for_visit = $request->input('reason_for_visit');
            $appointment->notes = $request->input('notes');
            $appointment->status = $request->input('status', 'pending');
            $appointment->booked_by = auth()->id();
            $appointment->booked_via = 'patient';
            $appointment->save();

            // Return success response
            return response()->json([
                'status' => 200,
                'msg' => 'Appointment created successfully.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'status' => 422,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Appointment creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password']),
            ]);

            // Handle exceptions and return a response
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
