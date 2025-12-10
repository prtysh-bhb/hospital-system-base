<?php

namespace App\Services\admin;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PetientService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getPatients($filters = [])
    {
        $query = PatientProfile::with('user');

        // Search by name, email, or phone
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by blood group
        if (! empty($filters['blood_group'])) {
            $query->where('blood_group', $filters['blood_group']);
        }

        // Filter by status
        if (! empty($filters['status'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getPatientById($id)
    {
        return PatientProfile::with('user')->findOrFail($id);
    }

    public function updatePatient($id, array $data)
    {
        try {
            DB::beginTransaction();

            \Log::info('Updating patient', ['id' => $id, 'data' => $data]);

            $patient = PatientProfile::findOrFail($id);
            $user = $patient->user;

            if (! $user) {
                throw new \Exception('User not found for patient profile');
            }

            // Update user data
            $userUpdateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'],
            ];

            \Log::info('Updating user', ['user_id' => $user->id, 'data' => $userUpdateData]);
            $user->update($userUpdateData);

            // Update patient profile data
            $patientUpdateData = [
                'blood_group' => $data['blood_group'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            ];

            \Log::info('Updating patient profile', ['patient_id' => $patient->id, 'data' => $patientUpdateData]);
            $patient->update($patientUpdateData);

            DB::commit();
            \Log::info('Patient updated successfully', ['id' => $id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating patient: '.$e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function deletePatient($id)
    {
        try {
            DB::beginTransaction();

            // Find user by ID
            $user = User::findOrFail($id);

            // Find associated patient profile
            $patient = PatientProfile::where('user_id', $user->id)->first();

            if (! $patient) {
                throw new \Exception('Patient profile not found for user');
            }

            // Soft delete patient profile
            $patient->delete();

            // Soft delete user
            $user->delete();

            DB::commit();

            \Log::info('Patient deleted successfully', ['user_id' => $id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting patient: '.$e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
