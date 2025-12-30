<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected CalendarService $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the calendar page
     */
    public function index(Request $request)
    {
        // Get current year and month, or from request
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $doctorId = $request->input('doctor_id', null);

        // Get calendar data
        $calendarData = $this->calendarService->getCalendarData($year, $month, $doctorId);

        // Get all doctors for filter
        $doctors = $this->calendarService->getAllDoctors();

        // Get month statistics
        $statistics = $this->calendarService->getMonthStatistics($year, $month, $doctorId);

        return view('admin.calendar', compact('calendarData', 'doctors', 'statistics', 'doctorId'));
    }

    /**
     * Get appointments for a specific date (AJAX)
     */
    public function getDateAppointments(Request $request)
    {
        $date = $request->input('date');
        $doctorId = $request->input('doctor_id', null);

        if (! $date) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required',
            ], 400);
        }

        $appointments = $this->calendarService->getDateAppointments($date, $doctorId);

        return response()->json([
            'success' => true,
            'date' => Carbon::parse($date)->format('F d, Y'),
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get single appointment details (AJAX)
     */
    public function getAppointmentDetails($id)
    {
        try {
            $appointment = \App\Models\Appointment::with(['patient', 'doctor.doctorProfile.specialty'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'appointment' => [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'status' => $appointment->status,
                    'formatted_date' => $appointment->formatted_date,
                    'formatted_time' => $appointment->formatted_time,
                    'duration_minutes' => $appointment->duration_minutes,
                    'appointment_type' => $appointment->appointment_type,
                    'reason_for_visit' => $appointment->reason_for_visit,
                    'symptoms' => $appointment->symptoms,
                    'notes' => $appointment->notes,
                    'patient_name' => $appointment->patient->first_name.' '.$appointment->patient->last_name,
                    'patient_phone' => $appointment->patient->phone,
                    'patient_email' => $appointment->patient->email,
                    'doctor_name' => 'Dr. '.$appointment->doctor->first_name.' '.$appointment->doctor->last_name,
                    'doctor_specialty' => $appointment->doctor->doctorProfile?->specialty?->name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found',
            ], 404);
        }
    }

    /**
     * Get week view data (AJAX)
     */
    public function getWeekView(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
            $doctorId = $request->input('doctor_id', null);

            $weekData = $this->calendarService->getWeekData($startDate, $doctorId);
            $statistics = $this->calendarService->getWeekStatistics($startDate, $doctorId);

            return response()->json([
                'success' => true,
                'data' => $weekData,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load week view',
            ], 400);
        }
    }

    /**
     * Get day view data (AJAX)
     */
    public function getDayView(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $doctorId = $request->input('doctor_id', null);

            $dayData = $this->calendarService->getDayData($date, $doctorId);
            $statistics = $this->calendarService->getDayStatistics($date, $doctorId);

            return response()->json([
                'success' => true,
                'data' => $dayData,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load day view',
            ], 400);
        }
    }

    /**
     * Get month view data (AJAX)
     */
    public function getMonthView(Request $request)
    {
        try {
            $year = $request->input('year', Carbon::now()->year);
            $month = $request->input('month', Carbon::now()->month);
            $doctorId = $request->input('doctor_id', null);

            $calendarData = $this->calendarService->getCalendarData($year, $month, $doctorId);
            $statistics = $this->calendarService->getMonthStatistics($year, $month, $doctorId);

            return response()->json([
                'success' => true,
                'data' => $calendarData,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load month view',
            ], 400);
        }
    }
}
