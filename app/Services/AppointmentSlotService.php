<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class AppointmentSlotService
{
    /**
     * Get available time slots for a doctor on a specific date
     *
     * @param  int  $doctorId
     * @param  string  $date
     * @param  int|null  $excludeAppointmentId  - exclude this appointment when checking (for updates)
     * @return array
     */
    public function getAvailableSlots($doctorId, $date, $excludeAppointmentId = null)
    {
        if (!$doctorId || !$date) {
            return [
                'success' => false,
                'message' => 'Doctor and date are required',
                'slots' => [],
            ];
        }

        // Ensure $date is Carbon instance
        $selectedDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        $now = Carbon::now();
        $isToday = $selectedDate->isToday();
        $weekday = $selectedDate->dayOfWeek;

        // Check if doctor is on leave for this date
        $leaveCheck = $this->isDoctorOnLeave($doctorId, $selectedDate->format('Y-m-d'));
        if ($leaveCheck['on_leave'] && $leaveCheck['leave_type'] === 'full_day') {
            return [
                'success' => false,
                'message' => $leaveCheck['message'],
                'slots' => [],
                'on_leave' => true,
                'leave_end_date' => $leaveCheck['leave_end_date'],
            ];
        }

        // Get doctor schedule
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $weekday)
            ->where('is_available', true)
            ->first();

        if (!$schedule || !$schedule->start_time || !$schedule->end_time) {
            return [
                'success' => false,
                'message' => 'Doctor schedule not available for this date.',
                'slots' => [],
                'on_leave' => false,
            ];
        }

        $slots = [];
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $slotDuration = $schedule->slot_duration ?? 30;

        // Get booked appointments for this doctor on this date
        $bookedQuery = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $selectedDate->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress']);

        if ($excludeAppointmentId) {
            $bookedQuery->where('id', '!=', $excludeAppointmentId);
        }

        $bookedSlots = $bookedQuery->pluck('appointment_time')
            ->map(function ($time) {
                try {
                    return Carbon::parse($time)->format('H:i');
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->toArray();

        while ($start < $end) {
            $timeSlot24 = $start->format('H:i');
            $timeSlot12 = $start->format('h:i A');

            // Skip booked slots
            if (in_array($timeSlot24, $bookedSlots)) {
                $start->addMinutes($slotDuration);
                continue;
            }

            // Skip half-day leave slots
            if ($leaveCheck['on_leave'] && $leaveCheck['leave_type'] === 'half_day') {
                if ($this->isSlotDuringHalfDayLeave($start, $leaveCheck)) {
                    $start->addMinutes($slotDuration);
                    continue;
                }
            }

            // For today, skip slots within 30 minutes
            if ($isToday) {
                $slotDateTime = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $timeSlot24);
                if ($slotDateTime->gt($now->copy()->addMinutes(30))) {
                    $slots[] = $timeSlot12;
                }
            } else {
                $slots[] = $timeSlot12;
            }

            $start->addMinutes($slotDuration);
        }

        return [
            'success' => true,
            'slots' => $slots,
            'message' => count($slots) > 0 ? 'Slots available' : 'No slots available',
            'on_leave' => false,
        ];
    }

    /**
     * Check if doctor is on leave for a specific date
     *
     * @param  int  $doctorId
     * @param  string  $date
     * @return array
     */
    public function isDoctorOnLeave($doctorId, $date)
    {
        $leave = DoctorLeave::where('doctor_id', $doctorId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($leave) {
            $endDate = Carbon::parse($leave->end_date)->format('d M, Y');
            $message = $leave->leave_type === 'half_day'
                ? 'Doctor is on half-day leave on this date. Limited slots available.'
                : "Doctor is on leave until {$endDate}. Please select another date or doctor.";

            return [
                'on_leave' => true,
                'message' => $message,
                'leave_end_date' => $leave->end_date,
                'leave_type' => $leave->leave_type,
                'half_day_slot' => $leave->half_day_slot ?? null,
                'start_time' => $leave->start_time ?? null,
                'end_time' => $leave->end_time ?? null,
            ];
        }

        return [
            'on_leave' => false,
            'message' => null,
            'leave_end_date' => null,
            'leave_type' => null,
        ];
    }

    /**
     * Check if a time slot is during half-day leave
     *
     * @param  Carbon  $slotTime
     * @param  array  $leaveCheck
     * @return bool
     */
    private function isSlotDuringHalfDayLeave($slotTime, $leaveCheck)
    {
        if ($leaveCheck['leave_type'] !== 'half_day')
            return false;

        if ($leaveCheck['start_time'] && $leaveCheck['end_time']) {
            $leaveStart = Carbon::parse($leaveCheck['start_time']);
            $leaveEnd = Carbon::parse($leaveCheck['end_time']);
            return $slotTime->gte($leaveStart) && $slotTime->lt($leaveEnd);
        }

        if ($leaveCheck['half_day_slot']) {
            $noon = Carbon::parse('12:00');
            if ($leaveCheck['half_day_slot'] === 'morning')
                return $slotTime->lt($noon);
            if ($leaveCheck['half_day_slot'] === 'afternoon')
                return $slotTime->gte($noon);
        }

        return false;
    }

    public function isSlotAvailable($doctorId, $date, $time, $excludeAppointmentId = null)
    {
        $time24 = $this->convertTo24Hour($time);
        $selectedDate = Carbon::parse($date);
        $now = Carbon::now();

        if ($selectedDate->isToday()) {
            $slotDateTime = Carbon::parse($date . ' ' . $time24);
            if ($slotDateTime->lte($now->copy()->addMinutes(30)))
                return false;
        }

        $weekday = $selectedDate->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $weekday)
            ->where('is_available', true)
            ->first();

        if (!$schedule)
            return false;

        $slotTime = Carbon::parse($time24);
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        if ($slotTime->lt($startTime) || $slotTime->gte($endTime))
            return false;

        $query = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->where(function ($q) use ($time24) {
                $q->where('appointment_time', $time24)
                    ->orWhere('appointment_time', $time24 . ':00');
            });

        if ($excludeAppointmentId)
            $query->where('id', '!=', $excludeAppointmentId);

        return !$query->exists();
    }

    private function convertTo24Hour($time)
    {
        if (!preg_match('/(AM|PM|am|pm)/', $time)) {
            return substr_count($time, ':') === 1 ? $time . ':00' : $time;
        }

        try {
            return date('H:i:s', strtotime($time));
        } catch (\Exception $e) {
            return $time;
        }
    }

    public function validateAppointmentTime($doctorId, $date, $time, $excludeAppointmentId = null)
    {
        // Check if doctor is on leave
        $leaveCheck = $this->isDoctorOnLeave($doctorId, $date);
        if ($leaveCheck['on_leave']) {
            // For full-day leave, reject completely
            if ($leaveCheck['leave_type'] === 'full_day') {
                return [
                    'valid' => false,
                    'message' => $leaveCheck['message'],
                ];
            }

            // For half-day leave, check if the time slot falls during leave period
            if ($leaveCheck['leave_type'] === 'half_day') {
                $slotTime = Carbon::parse($this->convertTo24Hour($time));
                if ($this->isSlotDuringHalfDayLeave($slotTime, $leaveCheck)) {
                    return [
                        'valid' => false,
                        'message' => 'This time slot is not available due to doctor\'s half-day leave. Please select another time.',
                    ];
                }
            }
        }

        // Check if appointment date is in the past
        $selectedDate = Carbon::parse($date);
        $now = Carbon::now();

        if ($selectedDate->lt($now->startOfDay())) {
            return [
                'valid' => false,
                'message' => 'Cannot book appointments for past dates.',
            ];
        }

        // Check if time has passed for today
        if ($selectedDate->isToday()) {
            $time24 = $this->convertTo24Hour($time);
            $slotDateTime = Carbon::parse($date . ' ' . $time24);

            // Add 30-minute buffer to prevent booking slots that are too close to current time
            if ($slotDateTime->lte($now->copy()->addMinutes(30))) {
                return [
                    'valid' => false,
                    'message' => 'Cannot book appointments for past time slots or slots within 30 minutes. Please select a future time.',
                ];
            }
        }

        if (!$this->isSlotAvailable($doctorId, $date, $time, $excludeAppointmentId)) {
            return [
                'valid' => false,
                'message' => 'This time slot is not available. Please select another time.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Time slot is available',
        ];
    }
}
