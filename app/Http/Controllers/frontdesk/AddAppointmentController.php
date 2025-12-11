<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppointmentSlotService;
use App\Services\public\BookAppointmentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddAppointmentController extends Controller
{
    protected $bookingService;

    protected $slotService;

    public function __construct(BookAppointmentService $bookingService, AppointmentSlotService $slotService)
    {
        $this->bookingService = $bookingService;
        $this->slotService = $slotService;
    }

    public function index()
    {
        return view('frontdesk.add-appointment');
    }

    public function searchPatient(Request $request)
    {
        $search = $request->get('search');

        $patients = User::where('role', 'patient')
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->with('patientProfile')
            ->limit(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->full_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                ];
            });

        return response()->json([
            'success' => true,
            'patients' => $patients,
        ]);
    }

    public function getDoctors(Request $request)
    {
        $doctors = User::where('role', 'doctor')
            ->where('status', 'active')
            ->with(['doctorProfile.specialty'])
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->full_name,
                    'specialty' => $doctor->doctorProfile->specialty->name ?? 'N/A',
                    'fee' => $doctor->doctorProfile->consultation_fee ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'doctors' => $doctors,
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $date = $request->get('date');

        $result = $this->slotService->getAvailableSlots($doctorId, $date);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'patient_id' => 'nullable|exists:users,id',
                'first_name' => 'required_without:patient_id|string|min:2|max:25',
                'last_name' => 'required_without:patient_id|string|min:2|max:25',
                'email' => 'required_without:patient_id|email|max:50',
                'phone' => 'required_without:patient_id|regex:/^[0-9]+$/|min:10|max:15',
                'date_of_birth' => 'required_without:patient_id|date|before:today',
                'gender' => 'required_without:patient_id|in:male,female,other',
                'doctor_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|string',
                'appointment_type' => 'required|in:consultation,follow_up,emergency,check_up',
                'reason_for_visit' => 'required|string|min:10|max:100',
                'notes' => 'nullable|string|max:100',
            ], [

                // patient_id
                'patient_id.exists' => 'The selected patient is invalid.',

                // first_name
                'first_name.required_without' => 'The first name is required when patient is not selected.',
                'first_name.string' => 'The first name must be a valid string.',
                'first_name.min' => 'The first name must be at least 2 characters.',
                'first_name.max' => 'The first name may not be greater than 25 characters.',

                // last_name
                'last_name.required_without' => 'The last name is required when patient is not selected.',
                'last_name.string' => 'The last name must be a valid string.',
                'last_name.min' => 'The last name must be at least 2 characters.',
                'last_name.max' => 'The last name may not be greater than 25 characters.',

                // email
                'email.required_without' => 'The email is required when patient is not selected.',
                'email.email' => 'The email must be a valid email address.',
                'email.max' => 'The email may not be greater than 50 characters.',

                // phone
                'phone.required_without' => 'The phone number is required when patient is not selected.',
                'phone.regex' => 'The phone number may only contain digits.',
                'phone.min' => 'The phone number must be at least 10 digits.',
                'phone.max' => 'The phone number may not be greater than 15 digits.',

                // date_of_birth
                'date_of_birth.required_without' => 'The date of birth is required when patient is not selected.',
                'date_of_birth.date' => 'The date of birth must be a valid date.',
                'date_of_birth.before' => 'The date of birth must be a date before today.',

                // gender
                'gender.required_without' => 'The gender is required when patient is not selected.',
                'gender.in' => 'The selected gender is invalid.',

                // doctor_id
                'doctor_id.required' => 'The doctor field is required.',
                'doctor_id.exists' => 'The selected doctor is invalid.',

                // appointment_date
                'appointment_date.required' => 'The appointment date is required.',
                'appointment_date.date' => 'The appointment date must be a valid date.',
                'appointment_date.after_or_equal' => 'The appointment date must be today or a future date.',

                // appointment_time
                'appointment_time.required' => 'Selete the appointment time.',
                'appointment_time.string' => 'The appointment time must be a valid string.',

                // appointment_type
                'appointment_type.required' => 'Select the appointment type.',
                'appointment_type.in' => 'The selected appointment type is invalid.',

                // reason_for_visit
                'reason_for_visit.required' => 'The reason for visit is required.',
                'reason_for_visit.string' => 'The reason for visit must be a valid string.',
                'reason_for_visit.min' => 'The reason for visit must be at least 10 characters.',
                'reason_for_visit.max' => 'The reason for visit may not be greater than 100 characters.',

                // notes
                'notes.string' => 'The notes must be a valid string.',
                'notes.max' => 'The notes may not be greater than 100 characters.',

            ]);

            $appointmentData = [
                'doctor_id' => $validated['doctor_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'appointment_type' => $validated['appointment_type'],
                'reason_for_visit' => $validated['reason_for_visit'],
                'notes' => $validated['notes'] ?? null,
                'booked_via' => 'frontdesk',
            ];

            // If patient_id exists, use existing patient
            if (! empty($validated['patient_id'])) {
                $patient = User::find($validated['patient_id']);
                $appointmentData = array_merge($appointmentData, [
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                ]);
            } else {
                // Create new patient
                $appointmentData = array_merge($appointmentData, [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'] ?? null,
                ]);
            }
            $result = $this->bookingService->createAppointment($appointmentData);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment booked successfully',
                    'appointment_id' => $result['appointment_id'],
                    'appointment_number' => $result['appointment_number'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment',
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
