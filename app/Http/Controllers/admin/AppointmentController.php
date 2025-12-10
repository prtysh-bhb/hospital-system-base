<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PatientProfile;
use Illuminate\Validation\Rule;
use Symfony\Component\Clock\now;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\AppointmentSlotService;

class AppointmentController extends Controller
{
    protected $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    public function index()
    {
        $doctors = User::where('role', 'doctor')->get();

        return view('admin.appointments', compact('doctors'));
    }

    // Display the form to add an appointment
    public function addAppointments()
    {
        $patients = User::where('role', 'patient')->get();
        $doctors = User::where('role', 'doctor')->with('doctorProfile.specialty')->get();

        return view('admin.add-appointment', compact('patients', 'doctors'));
    }

    // Get available time slots for a doctor on a specific date
    public function getAvailableSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $date = $request->get('date');
        $excludeAppointmentId = $request->get('exclude_appointment_id');

        $result = $this->slotService->getAvailableSlots($doctorId, $date, $excludeAppointmentId);

        return response()->json($result);
    }

    public function getAppointments(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.doctorProfile.specialty']);

        // 1. SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('appointment_number', 'LIKE', "%$search%")
                    ->orWhereHas('patient', function ($pat) use ($search) {
                        $pat->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%");
                    });
            });
        }

        // 2. DOCTOR
        if ($request->filled('doctor_id') && $request->doctor_id != '') {
            $query->where('doctor_id', $request->doctor_id);
        }

        // 3. DATE
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        // 4. STATUS
        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    // Fetch the existing appointment details for a selected patient
    public function getPatientAppointmentDetails($patient_id)
    {
        $appointment = Appointment::where('patient_id', $patient_id)->latest()->first();

        // If the appointment exists, return the data
        if ($appointment) {
            return response()->json([
                'success' => true,
                'data' => $appointment,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No previous appointments found.',
        ]);
    }

    // Store or update an appointment
    public function storeAppointment(Request $request)
    {
        try {
            // Determine whether a patient was selected or new patient details are provided
            $patientId = $request->input('patient_id');

            if ($patientId && $patientId != '') {
                // Validate required fields when patient selected
                $request->validate([
                    'patient_id' => ['required', Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'patient');
                    })],
                    'doctor_id' => ['required', Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'doctor');
                    })],
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
                    'first_name' => 'required|string|regex:/^[A-Za-z\s]+$/|min:2|max:25',
                    'last_name' => 'required|string|regex:/^[A-Za-z\s]+$/|min:2|max:25',
                    'email' => 'required|email|max:50|unique:users,email',
                    'phone' => 'required|string|regex:/^[0-9]+$/|min:10|max:15',
                    'date_of_birth' => 'required|date|before_or_equal:today',
                    'gender' => 'required|in:male,female,other',
                    'address' => 'nullable|string|max:100',
                    'doctor_id' => ['required', Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'doctor');
                    })],
                    'appointment_date' => 'required|date|after_or_equal:today',
                    'appointment_time' => 'required',
                    'appointment_type' => 'required|in:consultation,follow_up,emergency,check_up',
                    'reason_for_visit' => 'required|string|max:1000',
                    'notes' => 'nullable|string|max:500',
                ], [
                    'first_name.required' => 'First name is required.',
                    'first_name.regex' => 'First name should only contain letters and spaces.',
                    'first_name.min' => 'First name must be at least 2 characters.',
                    'first_name.max' => 'First name cannot exceed 25 characters.',
                    'last_name.required' => 'Last name is required.',
                    'last_name.regex' => 'Last name should only contain letters and spaces.',
                    'last_name.min' => 'Last name must be at least 2 characters.',
                    'last_name.max' => 'Last name cannot exceed 25 characters.',
                    'email.required' => 'Email is required.',
                    'email.email' => 'Please enter a valid email address.',
                    'email.max' => 'Email cannot exceed 50 characters.',
                    'email.unique' => 'This email is already registered.',
                    'phone.required' => 'Phone number is required.',
                    'phone.regex' => 'Phone number must contain only digits.',
                    'phone.min' => 'Phone number must be at least 10 digits.',
                    'phone.max' => 'Phone number cannot exceed 15 digits.',
                    'date_of_birth.required' => 'Date of birth is required.',
                    'date_of_birth.before_or_equal' => 'Date of birth must be today or in the past.',
                    'gender.required' => 'Gender is required.',
                    'address.max' => 'Address cannot exceed 100 characters.',
                    'doctor_id.required' => 'Please select a doctor.',
                    'appointment_date.required' => 'Appointment date is required.',
                    'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
                    'appointment_time.required' => 'Appointment time is required.',
                    'appointment_type.required' => 'Please select appointment type.',
                    'reason_for_visit.required' => 'Reason for visit is required.',
                    'reason_for_visit.max' => 'Reason for visit cannot exceed 1000 characters.',
                    'notes.max' => 'Notes cannot exceed 500 characters.',
                ]);

                // Create patient user
                $user = User::create([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'gender' => $request->input('gender'),
                    'address' => $request->input('address'),
                    'role' => 'patient',
                    'status' => 'active',
                    'password' => Hash::make(Str::random(12)),
                ]);

                // Create patient profile (address is stored in users table, not here)
                PatientProfile::create([
                    'user_id' => $user->id,
                    'emergency_contact_name' => $request->input('emergency_contact_name'),
                    'emergency_contact_phone' => $request->input('emergency_contact_phone'),
                    'medical_history' => $request->input('medical_history'),
                ]);

                $patientId = $user->id;
            }

            // Generate appointment number
            // $appointmentNumber = 'APT-'.date('Y').'-'.str_pad(Appointment::count() + 1, 6, '0', STR_PAD_LEFT);
            
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

            if (! $slotValidation['valid']) {
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
            $appointment->status = $request->input('status', 'pending');  // Default status
            $appointment->booked_by = auth()->id();
            $appointment->booked_via = 'admin';

            // Save the appointment to the database
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
            \Log::error('Appointment creation failed: '.$e->getMessage(), [
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

    public function getAppointmentsmodal(Request $request)
    {
        $patients = User::where('role', 'patient')->get();
        $doctors = User::where('role', 'doctor')->with('doctorProfile.specialty')->get();

        $appointment = null;
        if ($request->has('appointment_id')) {
            $appointment = Appointment::with(['patient', 'doctor.doctorProfile.specialty'])->find($request->appointment_id);
        }

        return view('admin.edit-appointment-modal', compact('patients', 'doctors', 'appointment'));
    }

    // Update appointment
    public function updateAppointment(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'appointment_id' => [
                    'required',
                    'exists:appointments,id',
                ],
                'patient_id' => [
                    'required',
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'patient');
                    }),
                ],
                'doctor_id' => [
                    'required',
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'doctor');
                    }),
                ],
                'appointment_date' => [
                    'required',
                    'date',
                ],
                'appointment_time' => [
                    'required',
                    'date_format:H:i',
                ],
                'appointment_type' => [
                    'required',
                    'in:consultation,follow_up,emergency,check_up',
                ],
                'reason_for_visit' => [
                    'required',
                    'string',
                    'max:1000',
                ],
                'status' => [
                    'required',
                    'in:pending,confirmed,checked_in,in_progress,completed,cancelled,no_show',
                ],
            ], [
                'patient_id.required' => 'Please select a valid patient.',
                'patient_id.exists' => 'The selected patient does not exist or is not valid.',
                'doctor_id.required' => 'Please select a valid doctor.',
                'doctor_id.exists' => 'The selected doctor does not exist or is not valid.',
                'appointment_date.required' => 'Appointment date is required.',
                'appointment_date.date' => 'Please provide a valid date for the appointment.',
                'appointment_time.required' => 'Appointment time is required.',
                'appointment_time.date_format' => 'Please provide a valid time in the format HH:MM.',
                'appointment_type.required' => 'Please select the appointment type.',
                'appointment_type.in' => 'Please select a valid appointment type.',
                'reason_for_visit.required' => 'Reason for visit is required.',
                'reason_for_visit.max' => 'Reason for visit cannot be longer than 1000 characters.',
                'status.required' => 'Status is required.',
                'status.in' => 'Please select a valid status.',
            ]);

            // Find and update the appointment
            $appointment = Appointment::find($request->appointment_id);

            if (! $appointment) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found.',
                ]);
            }

            $appointment->patient_id = $request->input('patient_id');
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->appointment_date = $request->input('appointment_date');
            $appointment->appointment_time = $request->input('appointment_time');
            $appointment->appointment_type = $request->input('appointment_type');
            $appointment->reason_for_visit = $request->input('reason_for_visit');

            // Get the original status before changing
            $originalStatus = $appointment->getOriginal('status');
            $newStatus = $request->input('status');
            $appointment->status = $newStatus;
            $appointment->notes = $request->input('notes', $appointment->notes);

            // Check if reactivating a cancelled/no_show appointment
            $inactiveStatuses = ['cancelled', 'no_show', 'completed'];
            $activeStatuses = ['pending', 'confirmed', 'checked_in', 'in_progress'];

            if (in_array($originalStatus, $inactiveStatuses) && in_array($newStatus, $activeStatuses)) {
                // Validate that the slot is still available (don't exclude current appointment since it was inactive)
                $slotValidation = $this->slotService->validateAppointmentTime(
                    $request->input('doctor_id'),
                    $request->input('appointment_date'),
                    $request->input('appointment_time'),
                    null // Don't exclude - we need to check if slot is truly available
                );

                if (! $slotValidation['valid']) {
                    return response()->json([
                        'status' => 422,
                        'msg' => 'Cannot reactivate appointment: '.$slotValidation['message'],
                        'errors' => ['status' => ['This time slot is now taken by another appointment. Please select a different time or keep the appointment cancelled.']],
                    ], 422);
                }
            }

            // Validate time slot if doctor, date, or time changed (for non-cancelled appointments)
            if ($appointment->isDirty(['doctor_id', 'appointment_date', 'appointment_time']) && ! in_array($newStatus, $inactiveStatuses)) {
                $slotValidation = $this->slotService->validateAppointmentTime(
                    $request->input('doctor_id'),
                    $request->input('appointment_date'),
                    $request->input('appointment_time'),
                    $appointment->id // Exclude current appointment
                );

                if (! $slotValidation['valid']) {
                    return response()->json([
                        'status' => 422,
                        'msg' => $slotValidation['message'],
                        'errors' => ['appointment_time' => [$slotValidation['message']]],
                    ]);
                }
            }

            $appointment->save();

            return response()->json([
                'status' => 200,
                'msg' => 'Appointment updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Delete appointment
    public function deleteAppointment(Request $request)
    {
        try {
            $validated = $request->validate([
                'appointment_id' => 'required|exists:appointments,id',
            ]);

            $appointment = Appointment::find($request->appointment_id);

            if (! $appointment) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found.',
                ]);
            }

            $appointment->delete();

            return response()->json([
                'status' => 200,
                'msg' => 'Appointment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    // View appointment details
    public function viewAppointment($id)
    {
        try {
            $appointment = Appointment::with(['patient', 'doctor.doctorProfile.specialty'])->find($id);

            if (! $appointment) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Appointment not found.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $appointment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
