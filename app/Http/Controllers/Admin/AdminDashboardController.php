<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Return dashboard stats as JSON for AJAX requests.
     */
    public function getDashboardDetails(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $totalPatients = User::where('role', 'patient')->count();
        $todaysAppointments = Appointment::whereDate('appointment_date', $today)->count();
        $totalDoctors = User::where('role', 'doctor')->count();

        $revenueToday = DB::table('appointments')
            ->whereDate('appointments.appointment_date', $today)
            ->join('doctor_profiles', 'appointments.doctor_id', '=', 'doctor_profiles.user_id')
            ->sum(DB::raw('COALESCE(doctor_profiles.consultation_fee, 0)'));

        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($a) {
                $statusMap = [
                    'confirmed' => ['label' => 'Confirmed', 'class' => 'text-green-700 bg-green-100'],
                    'pending' => ['label' => 'Pending', 'class' => 'text-yellow-700 bg-yellow-100'],
                    'checked_in' => ['label' => 'Checked In', 'class' => 'text-indigo-700 bg-indigo-100'],
                    'in_progress' => ['label' => 'In Progress', 'class' => 'text-blue-700 bg-blue-100'],
                    'completed' => ['label' => 'Completed', 'class' => 'text-emerald-700 bg-emerald-100'],
                    'cancelled' => ['label' => 'Cancelled', 'class' => 'text-red-700 bg-red-100'],
                    'no_show' => ['label' => 'No Show', 'class' => 'text-gray-700 bg-gray-100'],
                ];

                $status = $a->status ?? 'pending';
                $map = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'text-gray-700 bg-gray-100'];

                $doctorName = optional($a->doctor)->full_name;
                if (empty($doctorName) && $a->doctor_id) {
                    // include soft-deleted users in fallback lookup
                    $doc = User::withTrashed()->find($a->doctor_id);
                    if ($doc) {
                        $full = trim(($doc->first_name ?? '').' '.($doc->last_name ?? ''));
                        if (! empty($full)) {
                            $doctorName = $full;
                        } elseif (! empty($doc->email)) {
                            $doctorName = $doc->email;
                        } else {
                            $doctorName = 'Doctor #'.$a->doctor_id;
                        }
                    } else {
                        $doctorName = '—';
                    }
                }

                return [
                    'id' => $a->id,
                    'patient_name' => optional($a->patient)->full_name ?? '—',
                    'doctor_name' => $doctorName ?? '—',
                    'date' => optional($a->appointment_date) ? optional($a->appointment_date)->format('d-m-Y') : '',
                    'time' => optional($a->appointment_time) ? Carbon::parse($a->appointment_time)->format('h:i A') : '',
                    'status_label' => $map['label'],
                    'status_color' => $map['class'],
                ];
            });
        // return response()->json([...]);

        $activeDoctorsQuery = Appointment::whereDate('appointment_date', $today)
            ->select('doctor_id', DB::raw('count(*) as total'))
            ->groupBy('doctor_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $activeDoctors = $activeDoctorsQuery->map(function ($row) {
            $doctor = User::find($row->doctor_id);

            return [
                'id' => $doctor->id ?? null,
                'name' => $doctor->full_name ?? '—',
                'totalAppointments' => $row->total,
            ];
        });
        // dd($activeDoctorsQuery, $activeDoctors  );

        // Recent Activity from ActivityService (last 10)
        $recentActivity = ActivityService::getRecentActivities(10);

        return response()->json([
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'totalDoctors' => $totalDoctors,
            'revenueToday' => $revenueToday,
            'recentAppointments' => $recentAppointments,
            'activeDoctors' => $activeDoctors,
            'recentActivity' => $recentActivity,
        ]);
    }
}
