<?php

namespace App\Services\Admin;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get dashboard overview statistics.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        return [
            'today' => [
                'appointments' => Appointment::whereDate('appointment_date', $today)->count(),
                'completed' => Appointment::whereDate('appointment_date', $today)
                    ->where('status', 'completed')->count(),
                'pending' => Appointment::whereDate('appointment_date', $today)
                    ->where('status', 'pending')->count(),
                'revenue' => $this->calculateRevenue($today, $today),
            ],
            'this_month' => [
                'appointments' => Appointment::whereBetween('appointment_date', [$startOfMonth, $endOfMonth])->count(),
                'completed' => Appointment::whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
                    ->where('status', 'completed')->count(),
                'revenue' => $this->calculateRevenue($startOfMonth, $endOfMonth),
            ],
            'total' => [
                'patients' => User::where('role', 'patient')->count(),
                'doctors' => User::where('role', 'doctor')->count(),
                'appointments' => Appointment::count(),
                'revenue' => $this->calculateRevenue(),
            ],
        ];
    }

    /**
     * Get appointment trends over time.
     *
     * @param string $period 'daily', 'weekly', 'monthly'
     * @param int $days Number of days to go back
     * @return array
     */
    public function getAppointmentTrends(string $period = 'daily', int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $query = Appointment::selectRaw("
                DATE(appointment_date) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
            ")
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $data = $query->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'datasets' => [
                'total' => $data->pluck('total')->toArray(),
                'completed' => $data->pluck('completed')->toArray(),
                'cancelled' => $data->pluck('cancelled')->toArray(),
                'pending' => $data->pluck('pending')->toArray(),
            ],
        ];
    }

    /**
     * Get revenue analytics.
     *
     * @param int $days
     * @return array
     */
    public function getRevenueAnalytics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $revenueByDate = Appointment::select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as appointments'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count')
            )
            ->join('users', 'appointments.doctor_id', '=', 'users.id')
            ->join('doctor_profiles', 'users.id', '=', 'doctor_profiles.user_id')
            ->where('appointments.appointment_date', '>=', $startDate)
            ->where('appointments.status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate estimated revenue (completed appointments * avg consultation fee)
        $avgFee = DoctorProfile::avg('consultation_fee') ?? 500;

        return [
            'labels' => $revenueByDate->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'datasets' => [
                'appointments' => $revenueByDate->pluck('completed_count')->toArray(),
                'revenue' => $revenueByDate->pluck('completed_count')->map(function ($count) use ($avgFee) {
                    return $count * $avgFee;
                })->toArray(),
            ],
            'total_revenue' => $revenueByDate->sum('completed_count') * $avgFee,
            'avg_consultation_fee' => $avgFee,
        ];
    }

    /**
     * Get doctor performance metrics.
     *
     * @param int $limit
     * @return array
     */
    public function getDoctorPerformance(int $limit = 10): array
    {
        $doctors = User::where('role', 'doctor')
            ->with(['doctorProfile.specialty'])
            ->withCount([
                'doctorAppointments as total_appointments',
                'doctorAppointments as completed_appointments' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->having('total_appointments', '>', 0)
            ->orderByDesc('completed_appointments')
            ->limit($limit)
            ->get();

        return $doctors->map(function ($doctor) {
            $completionRate = $doctor->total_appointments > 0
                ? ($doctor->completed_appointments / $doctor->total_appointments) * 100
                : 0;

            $estimatedRevenue = $doctor->completed_appointments * ($doctor->doctorProfile->consultation_fee ?? 500);

            return [
                'id' => $doctor->id,
                'name' => $doctor->full_name,
                'specialty' => $doctor->doctorProfile->specialty->name ?? 'N/A',
                'total_appointments' => $doctor->total_appointments,
                'completed_appointments' => $doctor->completed_appointments,
                'completion_rate' => round($completionRate, 1),
                'consultation_fee' => $doctor->doctorProfile->consultation_fee ?? 0,
                'estimated_revenue' => $estimatedRevenue,
            ];
        })->toArray();
    }

    /**
     * Get appointment status breakdown.
     *
     * @return array
     */
    public function getAppointmentStatusBreakdown(): array
    {
        $statuses = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $total = $statuses->sum('count');

        return [
            'labels' => $statuses->pluck('status')->map(function ($status) {
                return ucfirst($status);
            })->toArray(),
            'data' => $statuses->pluck('count')->toArray(),
            'percentages' => $statuses->pluck('count')->map(function ($count) use ($total) {
                return $total > 0 ? round(($count / $total) * 100, 1) : 0;
            })->toArray(),
            'total' => $total,
        ];
    }

    /**
     * Get appointment type distribution.
     *
     * @return array
     */
    public function getAppointmentTypeDistribution(): array
    {
        $types = Appointment::select('appointment_type', DB::raw('COUNT(*) as count'))
            ->groupBy('appointment_type')
            ->get();

        return [
            'labels' => $types->pluck('appointment_type')->map(function ($type) {
                return ucfirst(str_replace('_', ' ', $type));
            })->toArray(),
            'data' => $types->pluck('count')->toArray(),
        ];
    }

    /**
     * Get patient visit trends.
     *
     * @return array
     */
    public function getPatientVisitTrends(): array
    {
        // Get new patients per month (last 12 months)
        $newPatients = User::where('role', 'patient')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get returning patients (those with multiple appointments)
        $returningPatients = Appointment::select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $totalPatients = User::where('role', 'patient')->count();

        return [
            'new_patients' => [
                'labels' => $newPatients->pluck('month')->map(function ($month) {
                    return Carbon::parse($month.'-01')->format('M Y');
                })->toArray(),
                'data' => $newPatients->pluck('count')->toArray(),
            ],
            'retention' => [
                'returning_patients' => $returningPatients,
                'total_patients' => $totalPatients,
                'retention_rate' => $totalPatients > 0
                    ? round(($returningPatients / $totalPatients) * 100, 1)
                    : 0,
            ],
        ];
    }

    /**
     * Get specialty-wise appointment distribution.
     *
     * @return array
     */
    public function getSpecialtyDistribution(): array
    {
        $specialties = Appointment::join('users', 'appointments.doctor_id', '=', 'users.id')
            ->join('doctor_profiles', 'users.id', '=', 'doctor_profiles.user_id')
            ->join('specialties', 'doctor_profiles.specialty_id', '=', 'specialties.id')
            ->select('specialties.name', DB::raw('COUNT(*) as count'))
            ->groupBy('specialties.name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $specialties->pluck('name')->toArray(),
            'data' => $specialties->pluck('count')->toArray(),
        ];
    }

    /**
     * Get peak hours analysis.
     *
     * @return array
     */
    public function getPeakHoursAnalysis(): array
    {
        $hourlyData = Appointment::select(
                DB::raw('HOUR(appointment_time) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'labels' => $hourlyData->pluck('hour')->map(function ($hour) {
                return sprintf('%02d:00', $hour);
            })->toArray(),
            'data' => $hourlyData->pluck('count')->toArray(),
        ];
    }

    /**
     * Calculate total revenue between dates.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return float
     */
    private function calculateRevenue(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = Appointment::join('users', 'appointments.doctor_id', '=', 'users.id')
            ->join('doctor_profiles', 'users.id', '=', 'doctor_profiles.user_id')
            ->where('appointments.status', 'completed');

        if ($startDate && $endDate) {
            $query->whereBetween('appointments.appointment_date', [$startDate, $endDate]);
        }

        $completedAppointments = $query->count();
        $avgFee = DoctorProfile::avg('consultation_fee') ?? 500;

        return $completedAppointments * $avgFee;
    }

    /**
     * Export analytics data to CSV format.
     *
     * @param string $type
     * @param array $filters
     * @return array
     */
    public function exportAnalytics(string $type, array $filters = []): array
    {
        // This method can be expanded based on specific export requirements
        $data = [];

        switch ($type) {
            case 'appointments':
                $data = $this->getAppointmentTrends('daily', $filters['days'] ?? 30);
                break;
            case 'revenue':
                $data = $this->getRevenueAnalytics($filters['days'] ?? 30);
                break;
            case 'doctors':
                $data = $this->getDoctorPerformance($filters['limit'] ?? 10);
                break;
            default:
                $data = $this->getDashboardStats();
        }

        return $data;
    }
}
