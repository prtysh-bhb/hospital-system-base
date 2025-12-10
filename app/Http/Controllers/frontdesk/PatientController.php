<?php

namespace App\Http\Controllers\frontdesk;

use App\Http\Controllers\Controller;
use App\Services\frontdesk\petientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(petientService $patientService)
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
        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|max:50|email|unique:users,email,'.$id,
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$id],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:25',
            'emergency_contact_phone' => 'nullable|regex:/^[0-9]{10,15}$/',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 25 characters.',
            'first_name.regex' => 'First name can only contain letters and spaces.',

            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 25 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',

            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 50 characters.',
            'email.unique' => 'This email is already taken.',

            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be between 10 and 15 digits.',
            'phone.unique' => 'This phone number is already taken.',

            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be before today.',

            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',

            'address.string' => 'Address must be a string.',
            'address.max' => 'Address cannot exceed 100 characters.',

            'blood_group.string' => 'Blood group must be a string.',
            'blood_group.max' => 'Blood group cannot exceed 10 characters.',

            'emergency_contact_name.string' => 'Emergency contact name must be a string.',
            'emergency_contact_name.max' => 'Emergency contact name cannot exceed 25 characters.',

            'emergency_contact_phone.regex' => 'Emergency contact phone must be between 10 and 15 digits.',
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
