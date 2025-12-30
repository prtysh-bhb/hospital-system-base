<?php

namespace App\Services\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DoctorDashboadService
{
    /**
     * Get dashboard statistics for the logged-in doctor
     */
    public function getDashboardStats()
    {
        $doctorId = Auth::id();
        $today = Carbon::today();

        // Today's appointments count
        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->count();

        // Completed appointments today
        $completedToday = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->count();

        // Pending appointments today
        $pendingToday = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        // Total patients (unique)
        $totalPatients = Appointment::where('doctor_id', $doctorId)
            ->distinct('patient_id')
            ->count('patient_id');

        return [
            'today_appointments' => $todayAppointments,
            'completed_today' => $completedToday,
            'pending_today' => $pendingToday,
            'total_patients' => $totalPatients,
        ];
    }

    /**
     * Get today's appointments for the logged-in doctor
     */
    public function getTodayAppointments()
    {
        $doctorId = Auth::id();
        $today = Carbon::today();

        return Appointment::with(['patient'])
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time', 'asc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'patient_name' => $appointment->patient->first_name.' '.$appointment->patient->last_name,
                    'patient_age' => $appointment->patient->date_of_birth ? Carbon::parse($appointment->patient->date_of_birth)->age : 'N/A',
                    'patient_gender' => ucfirst($appointment->patient->gender ?? 'N/A'),
                    'patient_blood_group' => $appointment->patient->patientProfile->blood_group ?? 'N/A',
                    'appointment_time' => Carbon::parse($appointment->appointment_time)->format('h:i A'),
                    'reason' => $appointment->reason_for_visit,
                    'status' => $appointment->status,
                    'appointment_type' => ucfirst(str_replace('_', ' ', $appointment->appointment_type)),
                ];
            });
    }

    /**
     * Get upcoming appointments (next 7 days excluding today)
     */
    public function getUpcomingAppointments()
    {
        $doctorId = Auth::id();
        $tomorrow = Carbon::tomorrow();
        $nextWeek = Carbon::today()->addDays(7);

        return Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$tomorrow, $nextWeek])
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    /**
     * Get recent patients (last 5)
     */
    public function getRecentPatients()
    {
        $doctorId = Auth::id();

        return Appointment::with(['patient'])
            ->where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'patient_id' => $appointment->patient_id,
                    'patient_name' => $appointment->patient->first_name.' '.$appointment->patient->last_name,
                    'last_visit' => Carbon::parse($appointment->appointment_date)->format('M d, Y'),
                    'condition' => $appointment->reason_for_visit,
                ];
            });
    }
}
