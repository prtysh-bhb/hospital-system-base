<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Services\Doctor\CalendarService;
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
    public function index()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return view('doctor.calendar', compact('currentYear', 'currentMonth'));
    }

    /**
     * Get calendar data for a specific month (AJAX)
     */
    public function getCalendarData(Request $request)
    {
        try {
            $yearMonth = $request->input('month', Carbon::now()->format('Y-m'));
            [$year, $month] = explode('-', $yearMonth);

            \Log::info('Loading calendar for doctor', [
                'doctor_id' => auth()->id(),
                'year' => $year,
                'month' => $month,
            ]);

            $calendarData = $this->calendarService->getCalendarData((int) $year, (int) $month);

            return response()->json([
                'success' => true,
                'data' => $calendarData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Calendar data error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load calendar data',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get weekly schedule (AJAX)
     */
    public function getWeeklySchedule()
    {
        $schedule = $this->calendarService->getWeeklySchedule();

        return response()->json([
            'success' => true,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Get appointments for a specific date (AJAX)
     */
    public function getDateAppointments(Request $request)
    {
        $date = $request->input('date');

        if (! $date) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required',
            ], 400);
        }

        $appointments = $this->calendarService->getDateAppointments($date);

        return response()->json([
            'success' => true,
            'date' => Carbon::parse($date)->format('F d, Y'),
            'appointments' => $appointments,
        ]);
    }

    /**
     * Update weekly schedule (AJAX)
     */
    public function updateSchedule(Request $request)
    {
        try {
            $validated = $request->validate([
                'schedules' => 'required|array',
                'schedules.*.day_of_week' => 'required|integer|between:0,6',
                'schedules.*.is_available' => 'required|boolean',
                'schedules.*.start_time' => 'nullable|date_format:H:i',
                'schedules.*.end_time' => 'nullable|date_format:H:i',
                'schedules.*.slot_duration' => 'nullable|integer|min:5',
            ]);

            $result = $this->calendarService->updateSchedule($validated['schedules']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'schedule' => $result,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Schedule update error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
