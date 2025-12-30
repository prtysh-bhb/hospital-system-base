<?php

namespace App\Services\Frontdesk;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PatientService
{
    public function getPatients($filters = [])
    {
        $query = User::where('role', 'patient')
            ->with(['patientProfile', 'patientAppointments' => function ($q) {
                $q->with('doctor')->latest('appointment_date')->limit(1);
            }]);

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Gender filter
        if (! empty($filters['gender']) && $filters['gender'] !== 'all') {
            $query->where('gender', $filters['gender']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getPatientById($id)
    {
        return User::where('role', 'patient')
            ->with(['patientProfile', 'patientAppointments' => function ($q) {
                $q->with('doctor')->latest('appointment_date');
            }])
            ->findOrFail($id);
    }

    public function updatePatient($id, $data)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            $user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => $data['address'] ?? null,
            ]);

            if ($user->patientProfile) {
                $user->patientProfile->update([
                    'blood_group' => $data['blood_group'] ?? null,
                    'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                    'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                    'medical_history' => $data['medical_history'] ?? null,
                    'current_medications' => $data['current_medications'] ?? null,
                    'insurance_provider' => $data['insurance_provider'] ?? null,
                    'insurance_number' => $data['insurance_number'] ?? null,
                ]);
            }

            DB::commit();

            return $user->fresh('patientProfile');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletePatient($id)
    {
        $user = User::find($id);

        if (! $user || $user->role !== 'patient') {
            return false;
        }
        $user->patientProfile()->delete();

        return $user->delete();
    }
}
