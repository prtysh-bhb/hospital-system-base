<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FrontDashboardController extends Controller
{
    public function index()
    {
        return view('frontdesk.dashboard');
    }

    public function getDashboardStats(Request $request)
    {
        $today = Carbon::today();

        // Today's appointments
        $todayAppointments = Appointment::with(['patient', 'doctor.doctorProfile.specialty'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'time' => $appointment->formatted_time,
                    'patient_name' => $appointment->patient->full_name ?? 'N/A',
                    'doctor_name' => $appointment->doctor->full_name ?? 'N/A',
                    'doctor_specialty' => $appointment->doctor->doctorProfile->specialty->name ?? 'N/A',
                    'status' => $appointment->status,
                    'status_color' => $this->getStatusColor($appointment->status),
                ];
            });

        // Stats
        $stats = [
            'today_total' => Appointment::whereDate('appointment_date', $today)->count(),
            'waiting' => Appointment::whereDate('appointment_date', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'available_doctors' => User::where('role', 'doctor')
                ->where('status', 'active')
                ->count(),
        ];

        // Recent Activities (last 10)
        $recentActivities = ActivityService::getRecentActivities(10);

        return response()->json([
            'success' => true,
            'appointments' => $todayAppointments,
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'current_date' => $today->format('F j, Y'),
        ]);
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            'confirmed' => 'green',
            'pending' => 'amber',
            'in_progress' => 'sky',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
