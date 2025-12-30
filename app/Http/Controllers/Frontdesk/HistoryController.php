<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Services\Frontdesk\HistoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoryController extends Controller
{
    protected $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    /**
     * Display appointment history page
     */
    public function index(Request $request)
    {
        $filters = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'status' => $request->status,
            'search' => $request->search,
        ];

        // Check if it's an AJAX request or expects JSON
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            try {
                $appointments = $this->historyService->getAppointmentsHistory($filters);
                $statistics = $this->historyService->getStatistics($filters);

                return response()->json([
                    'success' => true,
                    'appointments' => $appointments->items(),
                    'statistics' => $statistics,
                    'pagination' => [
                        'current_page' => $appointments->currentPage(),
                        'last_page' => $appointments->lastPage(),
                        'per_page' => $appointments->perPage(),
                        'total' => $appointments->total(),
                        'from' => $appointments->firstItem(),
                        'to' => $appointments->lastItem(),
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        return view('frontdesk.history');
    }

    /**
     * Export appointment history to CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'status' => $request->status,
            'search' => $request->search,
        ];

        $appointments = $this->historyService->getAppointmentsForExport($filters);
        $csvData = $this->historyService->generateCsvContent($appointments);

        // Generate filename with date range
        $filename = 'appointment_history';
        if ($request->from_date) {
            $filename .= '_from_'.$request->from_date;
        }
        if ($request->to_date) {
            $filename .= '_to_'.$request->to_date;
        }
        $filename .= '_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get appointment details
     */
    public function show($id)
    {
        try {
            $history = $this->historyService->getAppointmentDetails($id);
            $appointment = $history->appointment;

            return response()->json([
                'success' => true,
                'appointment' => [
                    'id' => $history->id,
                    'appointment_number' => $appointment->appointment_number,
                    'patient_name' => $appointment->patient->full_name,
                    'patient_email' => $appointment->patient->email,
                    'patient_phone' => $appointment->patient->phone,
                    'doctor_name' => $appointment->doctor->full_name,
                    'appointment_date' => Carbon::parse($history->appointment_date)->format('M d, Y'),
                    'appointment_time' => Carbon::parse($history->appointment_time)->format('h:i A'),
                    'status' => $history->status,
                    'appointment_type' => $appointment->appointment_type,
                    'reason_for_visit' => $appointment->reason_for_visit,
                    'symptoms' => $appointment->symptoms,
                    'notes' => $history->note,
                    'cancellation_reason' => $appointment->cancellation_reason,
                    'specialization' => $appointment->doctor->doctorProfile->specialty->name ?? 'N/A',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found',
            ], 404);
        }
    }
}
