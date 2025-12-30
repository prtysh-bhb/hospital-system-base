<?php

namespace App\Services\Frontdesk;

use App\Models\AppointmentHistory;
use Carbon\Carbon;

class HistoryService
{
    /**
     * Get appointments history with filters
     */
    public function getAppointmentsHistory($filters = [])
    {
        $query = AppointmentHistory::with([
            'appointment.patient.patientProfile',
            'appointment.doctor.doctorProfile.specialty'
        ])->whereHas('appointment.patient')
            ->whereHas('appointment.doctor');

        // Date range
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('appointment_date', [
                $filters['from_date'],
                $filters['to_date']
            ]);
        }

        // Status
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->whereHas('appointment', function ($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('doctor', function ($dq) use ($search) {
                    $dq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }
    /**
     * Get all appointments for export (without pagination)
     */
    public function getAppointmentsForExport($filters = [])
    {
        $query = AppointmentHistory::with(['patient', 'doctor', 'patient.patientProfile', 'doctor.doctorProfile'])
            ->whereHas('patient')
            ->whereHas('doctor');

        // Date range filter - only apply if BOTH dates are provided
        $hasFromDate = isset($filters['from_date']) && ! empty($filters['from_date']);
        $hasToDate = isset($filters['to_date']) && ! empty($filters['to_date']);

        if ($hasFromDate && $hasToDate) {
            $query->where('appointment_date', '>=', $filters['from_date'])
                ->where('appointment_date', '<=', $filters['to_date']);
        }

        // Status filter
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Search filter
        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('doctor', function ($dq) use ($search) {
                        $dq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();
    }

    /**
     * Generate CSV content from appointments
     */
    public function generateCsvContent($appointments)
    {
        $csvData = [];

        // CSV Header
        $csvData[] = [
            'Appointment Number',
            'Date',
            'Time',
            'Patient Name',
            'Patient Email',
            'Patient Phone',
            'Doctor Name',
            'Specialty',
            'Status',
            'Appointment Type',
            'Reason for Visit',
            'Notes',
            'Cancellation Reason',
        ];

        // CSV Rows
        foreach ($appointments as $appointment) {
            $csvData[] = [
                $appointment->appointment_number,
                Carbon::parse($appointment->appointment_date)->format('Y-m-d'),
                Carbon::parse($appointment->appointment_time)->format('H:i'),
                $appointment->patient->full_name ?? 'N/A',
                $appointment->patient->email ?? 'N/A',
                $appointment->patient->phone ?? 'N/A',
                $appointment->doctor->full_name ?? 'N/A',
                $appointment->doctor->doctorProfile->specialty->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $appointment->status)),
                ucfirst(str_replace('_', ' ', $appointment->appointment_type ?? 'N/A')),
                $appointment->reason_for_visit ?? '',
                $appointment->notes ?? '',
                $appointment->cancellation_reason ?? '',
            ];
        }

        return $csvData;
    }

    /**
     * Get statistics for appointment history
     */
    public function getStatistics($filters = [])
    {
        $query = AppointmentHistory::query();

        // Apply date range only if BOTH dates are provided
        $hasFromDate = isset($filters['from_date']) && ! empty($filters['from_date']);
        $hasToDate = isset($filters['to_date']) && ! empty($filters['to_date']);

        if ($hasFromDate && $hasToDate) {
            $query->where('appointment_date', '>=', $filters['from_date'])
                ->where('appointment_date', '<=', $filters['to_date']);
        }

        $total = $query->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();
        $noShow = (clone $query)->where('status', 'no_show')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'completed_percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'cancelled' => $cancelled,
            'cancelled_percentage' => $total > 0 ? round(($cancelled / $total) * 100, 1) : 0,
            'no_show' => $noShow,
            'no_show_percentage' => $total > 0 ? round(($noShow / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get appointment details by ID
     */
    public function getAppointmentDetails($id)
    {
        return AppointmentHistory::with([
            'appointment.patient.patientProfile',
            'appointment.doctor.doctorProfile.specialty'
        ])->findOrFail($id);
    }
}
