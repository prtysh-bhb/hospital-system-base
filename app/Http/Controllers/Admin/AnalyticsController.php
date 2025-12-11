<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show analytics dashboard.
     */
    public function index()
    {
        $stats = $this->analyticsService->getDashboardStats();

        return view('admin.analytics.index', compact('stats'));
    }

    /**
     * Get dashboard statistics (AJAX).
     */
    public function getDashboardStats()
    {
        try {
            $stats = $this->analyticsService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointment trends.
     */
    public function getAppointmentTrends(Request $request)
    {
        try {
            $period = $request->get('period', 'daily');
            $days = $request->get('days', 30);

            $trends = $this->analyticsService->getAppointmentTrends($period, $days);

            return response()->json([
                'success' => true,
                'data' => $trends,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load appointment trends',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get revenue analytics.
     */
    public function getRevenueAnalytics(Request $request)
    {
        try {
            $days = $request->get('days', 30);

            $revenue = $this->analyticsService->getRevenueAnalytics($days);

            return response()->json([
                'success' => true,
                'data' => $revenue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load revenue analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor performance metrics.
     */
    public function getDoctorPerformance(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $performance = $this->analyticsService->getDoctorPerformance($limit);

            return response()->json([
                'success' => true,
                'data' => $performance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load doctor performance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointment status breakdown.
     */
    public function getStatusBreakdown()
    {
        try {
            $breakdown = $this->analyticsService->getAppointmentStatusBreakdown();

            return response()->json([
                'success' => true,
                'data' => $breakdown,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load status breakdown',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointment type distribution.
     */
    public function getTypeDistribution()
    {
        try {
            $distribution = $this->analyticsService->getAppointmentTypeDistribution();

            return response()->json([
                'success' => true,
                'data' => $distribution,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load type distribution',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get patient visit trends.
     */
    public function getPatientTrends()
    {
        try {
            $trends = $this->analyticsService->getPatientVisitTrends();

            return response()->json([
                'success' => true,
                'data' => $trends,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load patient trends',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specialty distribution.
     */
    public function getSpecialtyDistribution()
    {
        try {
            $distribution = $this->analyticsService->getSpecialtyDistribution();

            return response()->json([
                'success' => true,
                'data' => $distribution,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load specialty distribution',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get peak hours analysis.
     */
    public function getPeakHours()
    {
        try {
            $peakHours = $this->analyticsService->getPeakHoursAnalysis();

            return response()->json([
                'success' => true,
                'data' => $peakHours,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load peak hours',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'appointments');
            $filters = $request->except('type');

            $data = $this->analyticsService->exportAnalytics($type, $filters);

            // Convert to CSV
            $filename = "analytics_{$type}_".now()->format('Y-m-d_His').'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                // Add headers based on data type
                if (!empty($data)) {
                    $firstRow = is_array($data) ? reset($data) : $data;
                    if (is_array($firstRow)) {
                        fputcsv($file, array_keys($firstRow));

                        foreach ($data as $row) {
                            fputcsv($file, $row);
                        }
                    } else {
                        // Handle different data structures
                        fputcsv($file, ['Label', 'Value']);
                        foreach ($data as $key => $value) {
                            fputcsv($file, [$key, $value]);
                        }
                    }
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Analytics export failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to export analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
