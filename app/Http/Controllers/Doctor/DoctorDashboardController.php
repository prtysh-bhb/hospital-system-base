<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Services\Doctor\DoctorDashboadService;

class DoctorDashboardController extends Controller
{
    protected DoctorDashboadService $dashboardService;

    public function __construct(DoctorDashboadService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stats = $this->dashboardService->getDashboardStats();
        $todayAppointments = $this->dashboardService->getTodayAppointments();
        $upcomingCount = $this->dashboardService->getUpcomingAppointments();

        return view('doctor.dashboard', compact('stats', 'todayAppointments', 'upcomingCount'));
    }
}
