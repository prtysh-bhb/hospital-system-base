<?php

namespace App\Services\Doctor;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarService
{
    /**
     * Get calendar data for a specific month
     */
    public function getCalendarData($year, $month)
    {
        $doctorId = Auth::id();
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $today = Carbon::today()->format('Y-m-d');

        // Get all appointments for this month
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('patient')
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->appointment_date)->format('Y-m-d');
            });

        // Generate calendar days
        $calendarDays = [];

        // Start from the first day of the week that the month starts on
        // Use Carbon::SUNDAY to ensure week starts on Sunday to match calendar header
        $calendarStart = $startDate->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $endDate->copy()->endOfWeek(Carbon::SATURDAY);

        $currentDate = $calendarStart->copy();

        while ($currentDate <= $calendarEnd) {

            $dateKey = $currentDate->format('Y-m-d');

            $isCurrentMonth = $currentDate->month == $month;

            $dayAppointments = [];

            if ($isCurrentMonth && isset($appointments[$dateKey])) {

                $dayAppointments = $appointments[$dateKey]->map(function ($apt) {

                    // Skip appointments without patients

                    if (! $apt->patient) {

                        return null;

                    }

                    return [
                        'id' => $apt->id,
                        'time' => Carbon::parse($apt->appointment_time)->format('g:i A'),
                        'status' => $apt->status,
                        'patient_name' => $apt->patient->first_name.' '.$apt->patient->last_name,
                        'doctor_name' => 'Dr. '.Auth::user()->last_name,
                    ];

                })->filter()->values()->toArray();

            }

            $calendarDays[] = [
                'day' => $currentDate->day,
                'date' => $dateKey,
                'is_current_month' => $isCurrentMonth,
                'is_today' => $dateKey === $today,
                'appointments' => $dayAppointments,
            ];

            $currentDate->addDay();
        }

        return [
            'days' => $calendarDays,
            'month_name' => $startDate->format('F Y'),
            'prev_month' => $startDate->copy()->subMonth()->format('Y-m'),
            'next_month' => $startDate->copy()->addMonth()->format('Y-m'),
        ];
    }

    /**
     * Get doctor's weekly schedule
     */
    public function getWeeklySchedule()
    {
        $doctorId = Auth::id();

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $weeklySchedule = [];
        foreach ($days as $dayNum => $dayName) {
            $schedule = $schedules->get($dayNum);

            if ($schedule && $schedule->is_available) {
                $weeklySchedule[] = [
                    'day_of_week' => $dayNum,
                    'day_name' => $dayName,
                    'is_available' => true,
                    'start_time' => Carbon::parse($schedule->start_time)->format('g:i A'),
                    'end_time' => Carbon::parse($schedule->end_time)->format('g:i A'),
                    'slot_duration' => $schedule->slot_duration,
                ];
            } else {
                $weeklySchedule[] = [
                    'day_of_week' => $dayNum,
                    'day_name' => $dayName,
                    'is_available' => false,
                    'start_time' => null,
                    'end_time' => null,
                    'slot_duration' => null,
                ];
            }
        }

        return $weeklySchedule;
    }

    /**
     * Update weekly schedule
     */
    public function updateSchedule($schedules)
    {
        $doctorId = Auth::id();

        foreach ($schedules as $scheduleData) {
            // If day is unavailable, keep dummy times to satisfy NOT NULL constraint
            $startTime = $scheduleData['is_available'] && $scheduleData['start_time']
                ? $scheduleData['start_time']
                : '09:00';
            $endTime = $scheduleData['is_available'] && $scheduleData['end_time']
                ? $scheduleData['end_time']
                : '17:00';

            DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $doctorId,
                    'day_of_week' => $scheduleData['day_of_week'],
                ],
                [
                    'is_available' => $scheduleData['is_available'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration' => $scheduleData['slot_duration'] ?? 30,
                ]
            );
        }

        return $this->getWeeklySchedule();
    }

    /**
     * Get appointments for a specific date
     */
    public function getDateAppointments($date)
    {
        $doctorId = Auth::id();

        $appointments = Appointment::with('patient', 'doctor.doctorSchedules')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        return $appointments->map(function ($appointment) {

            $weekday = Carbon::parse($appointment->appointment_date)->dayOfWeek; // get appointment weekday
            $matchedSchedule = $appointment->doctor->doctorSchedules
                ->firstWhere('day_of_week', $weekday);  // Find our weekday schedule from doctorSchedules

            return [
                'id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'patient_name' => $appointment->patient->first_name.' '.$appointment->patient->last_name,
                'patient_age' => $appointment->patient->date_of_birth ?
                    Carbon::parse($appointment->patient->date_of_birth)->age : 'N/A',
                'time' => Carbon::parse($appointment->appointment_time)->format('g:i A'),
                'status' => $appointment->status,
                'reason' => $appointment->reason_for_visit,
                'type' => ucfirst(str_replace('_', ' ', $appointment->appointment_type)),
                'doctor_name' => 'Dr. '.$appointment->doctor->last_name,

                // ⭐ Slot Duration
                'duration' => $matchedSchedule ? $matchedSchedule->slot_duration : null,
            ];
        });
    }
}
