<?php

namespace App\Services\Admin;

use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;

class DoctoreServices
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getDoctors($filters = [])
    {
        $query = DoctorProfile::with('specialty', 'user')
            ->whereHas('user'); // Only include doctors with non-deleted users

        // Search by name, email, or phone
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })->orWhereHas('specialty', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by specialty
        if (! empty($filters['specialty_id'])) {
            $query->where('specialty_id', $filters['specialty_id']);
        }

        // Filter by status
        if (! empty($filters['status'])) {
            if ($filters['status'] === 'on_leave') {
                // Filter doctors who are currently on leave
                $query->whereHas('user.doctorLeaves', function ($q) {
                    $q->where('status', 'approved')
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                });
            } else {
                $query->whereHas('user', function ($q) use ($filters) {
                    $q->where('status', $filters['status']);
                });
            }
        }

        return $query->get();
    }

    public function createDoctor($data)
    {
        \DB::beginTransaction();

        try {
            // Create user with phone as password
            $user = User::create([
                'role' => 'doctor',
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => \Hash::make($data['phone']), // Phone as password
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'profile_image' => $data['profile_image'] ?? null,
                'status' => 'active',
            ]);

            // Create doctor profile
            $doctorProfile = DoctorProfile::create([
                'user_id' => $user->id,
                'specialty_id' => $data['specialty_id'],
                'qualification' => $data['qualification'],
                'experience_years' => $data['experience_years'],
                'license_number' => $data['license_number'],
                'consultation_fee' => $data['consultation_fee'],
                'bio' => $data['languages'] ?? null,
                'available_for_booking' => true,
            ]);

            // Create schedules if provided
            if (! empty($data['schedules'])) {
                foreach ($data['schedules'] as $dayOfWeek => $schedule) {
                    if (! empty($schedule['enabled'])) {
                        DoctorSchedule::create([
                            'doctor_id' => $user->id,
                            'day_of_week' => $dayOfWeek,
                            'start_time' => $schedule['start_time'],
                            'end_time' => $schedule['end_time'],
                            'slot_duration' => $data['slot_duration'],
                            'max_patients' => 20,
                            'is_available' => true,
                        ]);
                    }
                }
            }

            \DB::commit();

            return $doctorProfile;

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function getDoctorById($id)
    {
        return DoctorProfile::with('specialty', 'user', 'user.doctorSchedules')
            ->where('user_id', $id)
            ->first();
    }

    public function updateDoctor($id, $data)
    {
        \DB::beginTransaction();

        try {
            // Update user
            $user = User::findOrFail($id);
            $updateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => $data['address'],
            ];

            // Only update status if provided
            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            // Update profile image if provided
            if (isset($data['profile_image'])) {
                $updateData['profile_image'] = $data['profile_image'];
            }

            $user->update($updateData);

            // Update doctor profile
            $doctorProfile = DoctorProfile::where('user_id', $id)->firstOrFail();
            $profileData = [
                'specialty_id' => $data['specialty_id'],
                'qualification' => $data['qualification'],
                'experience_years' => $data['experience_years'],
                'license_number' => $data['license_number'],
                'consultation_fee' => $data['consultation_fee'],
                'bio' => $data['languages'] ?? null,
            ];

            // Only update available_for_booking if provided
            if (isset($data['available_for_booking'])) {
                $profileData['available_for_booking'] = $data['available_for_booking'];
            }

            $doctorProfile->update($profileData);

            // Update or create schedules
            // Always process schedules, even if array is empty (to handle deletions)
            // Track which days should be kept
            $updatedDays = [];

            if (isset($data['schedules']) && is_array($data['schedules'])) {
                // First, delete all existing schedules to avoid duplicate issues
                DoctorSchedule::where('doctor_id', $id)->forceDelete();

                foreach ($data['schedules'] as $dayOfWeek => $schedule) {
                    // Only create schedules where enabled is true
                    if (! empty($schedule['enabled']) && $schedule['enabled'] == '1' && isset($schedule['start_time']) && isset($schedule['end_time'])) {
                        DoctorSchedule::create([
                            'doctor_id' => $id,
                            'day_of_week' => (int) $dayOfWeek,
                            'start_time' => $schedule['start_time'],
                            'end_time' => $schedule['end_time'],
                            'slot_duration' => $data['slot_duration'],
                            'max_patients' => 20,
                            'is_available' => true,
                        ]);
                        $updatedDays[] = (int) $dayOfWeek;
                    }
                }
            } else {
                // If no schedules provided, delete all
                DoctorSchedule::where('doctor_id', $id)->forceDelete();
            }

            \DB::commit();

            return $doctorProfile;

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function deleteDoctor($id)
    {
        \DB::beginTransaction();

        try {
            $user = User::find($id);

            if (! $user || $user->role !== 'doctor') {
                \Log::warning("Delete failed: User not found or not a doctor. ID: {$id}");

                return false;
            }

            // Soft delete doctor schedules
            $schedulesDeleted = DoctorSchedule::where('doctor_id', $id)->delete();
            \Log::info("Deleted {$schedulesDeleted} schedules for doctor {$id}");

            // Soft delete doctor profile
            $profileDeleted = DoctorProfile::where('user_id', $id)->delete();
            \Log::info("Deleted {$profileDeleted} profile for doctor {$id}");

            // Soft delete user
            $userDeleted = $user->delete();
            \Log::info("Deleted user {$id}: ".($userDeleted ? 'success' : 'failed'));

            \DB::commit();

            return true;

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting doctor: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return false;
        }
    }
}
