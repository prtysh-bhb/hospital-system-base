<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Services\Frontdesk\DoctoreScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(DoctoreScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display doctor schedule page
     */
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $filters = [
            'specialty' => $request->specialty ?? 'all',
            'availability' => $request->availability ?? 'all',
        ];

        // Check if it's an AJAX request or expects JSON
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            try {
                $doctors = $this->scheduleService->getDoctorsSchedule($date, $filters);
                $specializations = $this->scheduleService->getSpecializations();

                return response()->json([
                    'success' => true,
                    'doctors' => $doctors,
                    'specializations' => $specializations,
                    'date' => Carbon::parse($date)->format('F d, Y'),
                    'day_name' => Carbon::parse($date)->format('l'),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        return view('frontdesk.doctor-schedule');
    }
}
