<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Services\Frontdesk\PatientService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'search' => $request->input('search'),
                'gender' => $request->input('gender'),
            ];

            $patients = $this->patientService->getPatients($filters);

            return response()->json([
                'success' => true,
                'patients' => $patients->items(),
                'pagination' => [
                    'total' => $patients->total(),
                    'per_page' => $patients->perPage(),
                    'current_page' => $patients->currentPage(),
                    'last_page' => $patients->lastPage(),
                    'from' => $patients->firstItem(),
                    'to' => $patients->lastItem(),
                ],
            ]);
        }

        return view('frontdesk.patients');
    }

    public function show(Request $request, $id)
    {
        try {
            $patient = $this->patientService->getPatientById($id);

            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'full_name' => $patient->full_name,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                    'profile_image' => $patient->profile_image,
                    'blood_group' => $patient->patientProfile->blood_group ?? null,
                    'emergency_contact_name' => $patient->patientProfile->emergency_contact_name ?? null,
                    'emergency_contact_phone' => $patient->patientProfile->emergency_contact_phone ?? null,
                    'medical_history' => $patient->patientProfile->medical_history ?? null,
                    'current_medications' => $patient->patientProfile->current_medications ?? null,
                    'insurance_provider' => $patient->patientProfile->insurance_provider ?? null,
                    'insurance_number' => $patient->patientProfile->insurance_number ?? null,
                    'last_appointment' => $patient->patientAppointments->first() ? [
                        'date' => $patient->patientAppointments->first()->appointment_date,
                        'doctor' => $patient->patientAppointments->first()->doctor->full_name ?? 'N/A',
                    ] : null,
                    'total_appointments' => $patient->patientAppointments->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found',
            ], 404);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'first_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
    //         'last_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
    //         'email' => 'required|email|unique:users,email,'.$id,
    //         'phone' => ['required', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$id],
    //         'date_of_birth' => 'required|date|before:today',
    //         'gender' => 'required|in:male,female,other',
    //         'address' => 'nullable|string|max:500',
    //         'blood_group' => 'nullable|string|max:10',
    //         'emergency_contact_name' => 'nullable|string|max:255',
    //         'emergency_contact_phone' => 'nullable|regex:/^[0-9]{10,15}$/',
    //     ]);

    //     try {
    //         $patient = $this->patientService->updatePatient($id, $validated);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Patient updated successfully',
    //             'patient' => $patient,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update patient',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:25',
                'regex:/^[a-zA-Z\s]+$/',
            ],

            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:25',
                'regex:/^[a-zA-Z\s]+$/',
            ],

            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:50',
                Rule::unique('users', 'email')->ignore($id),
            ],

            'phone' => [
                'required',
                'regex:/^[0-9]{10,15}$/',
                Rule::unique('users', 'phone')->ignore($id),
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before:today',
            ],

            'gender' => [
                'required',
                Rule::in(['male', 'female', 'other']),
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'blood_group' => [
                'nullable',
                Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:25',
                'required_with:emergency_contact_phone',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'regex:/^[0-9]{10,15}$/',
                'required_with:emergency_contact_name',
            ],

            'medical_history' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'current_medications' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'insurance_provider' => [
                'nullable',
                'string',
                'max:255',
            ],

            'insurance_number' => [
                'nullable',
                'string',
                'max:100',
            ],
        ],
            [
                'first_name.required' => 'First name is required.',
                'first_name.regex' => 'First name may contain only letters and spaces.',
                'last_name.required' => 'Last name is required.',
                'last_name.regex' => 'Last name may contain only letters and spaces.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Phone number must contain 10 to 15 digits.',
                'phone.unique' => 'This phone number is already in use.',
                'date_of_birth.required' => 'Date of birth is required.',
                'date_of_birth.before' => 'Date of birth must be in the past.',
                'gender.required' => 'Gender is required.',
                'gender.in' => 'Please select a valid gender.',
                'blood_group.in' => 'Please select a valid blood group.',
                'emergency_contact_name.required_with' => 'Emergency contact name is required when phone number is provided.',
                'emergency_contact_phone.required_with' => 'Emergency contact phone is required when name is provided.',
                'emergency_contact_phone.regex' => 'Emergency contact phone must be 10 to 15 digits.',
            ]);

        try {
            $patient = $this->patientService->updatePatient($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully.',
                'patient' => $patient,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update patient.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $result = $this->patientService->deletePatient($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Patient deleted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete patient',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the patient',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
