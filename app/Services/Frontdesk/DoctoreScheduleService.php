<?php

namespace App\Services\Frontdesk;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class DoctoreScheduleService
{
    /**
     * Get doctors with their schedules for a specific date
     */
    public function getDoctorsSchedule($date, $filters = [])
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0=Sunday, 1=Monday, etc.

        $query = User::with(['doctorProfile.specialty', 'doctorSchedules'])
            ->where('role', 'doctor')
            ->whereHas('doctorSchedules', function ($q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true);
            });

        // Filter by specialty
        if (isset($filters['specialty']) && $filters['specialty'] !== 'all') {
            $query->whereHas('doctorProfile.specialty', function ($q) use ($filters) {
                $q->where('name', $filters['specialty']);
            });
        }

        $doctors = $query->get();

        // Process each doctor's schedule
        return $doctors->map(function ($doctor) use ($date, $dayOfWeek, $filters) {
            $schedule = $doctor->doctorSchedules
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->first();

            if (! $schedule) {
                return null;
            }

            // Generate time slots
            $slots = $this->generateTimeSlots($doctor->id, $schedule, $date);

            // Calculate availability
            $availableSlots = collect($slots)->where('status', 'available')->count();
            $totalSlots = count($slots);

            // Determine doctor availability status
            $availabilityStatus = $this->getDoctorAvailabilityStatus($availableSlots, $totalSlots);

            // Filter by availability if specified
            if (isset($filters['availability']) && $filters['availability'] !== 'all') {
                if ($filters['availability'] !== $availabilityStatus) {
                    return null;
                }
            }
            
            return [
                'id' => $doctor->id,
                'name' => $doctor->full_name,
                'availability_status' =>$doctor->status,
                'specialization' => $doctor->doctorProfile->specialty->name ?? 'General',
                'experience' => $doctor->doctorProfile->experience_years ?? 0,
                'room' => $doctor->doctorProfile->room_number ?? 'N/A',
                'start_time' => Carbon::parse($schedule->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($schedule->end_time)->format('h:i A'),
                'available_slots' => $availableSlots,
                'total_slots' => $totalSlots,
                // 'availability_status' => $availabilityStatus,
                'slots' => $slots,
            ];
        })->filter(); // Remove null values
    }

    /**
     * Generate time slots for a doctor's schedule
     */
    private function generateTimeSlots($doctorId, $schedule, $date)
    {
        $slots = [];

        // Parse time fields more robustly - handle both time and datetime formats
        try {
            // Try to parse as time first
            if (strlen($schedule->start_time) <= 8) {
                // It's just a time value like "09:00:00"
                $startTime = Carbon::parse($date.' '.$schedule->start_time);
                $endTime = Carbon::parse($date.' '.$schedule->end_time);
            } else {
                // It might be a datetime, extract just the time part
                $startTime = Carbon::parse($date.' '.Carbon::parse($schedule->start_time)->format('H:i:s'));
                $endTime = Carbon::parse($date.' '.Carbon::parse($schedule->end_time)->format('H:i:s'));
            }
        } catch (\Exception $e) {
            // Fallback: try direct parsing
            $startTime = Carbon::parse($schedule->start_time)->setDateFrom(Carbon::parse($date));
            $endTime = Carbon::parse($schedule->end_time)->setDateFrom(Carbon::parse($date));
        }

        $slotDuration = $schedule->slot_duration ?? 30; // Default 30 minutes

        // Get all appointments for this doctor on this date
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress', 'completed'])
            ->get()
            ->keyBy(function ($apt) {
                try {
                    return Carbon::parse($apt->appointment_time)->format('H:i');
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter(); // Remove null keys

        $currentTime = $startTime->copy();

        while ($currentTime->lessThan($endTime)) {
            $timeKey = $currentTime->format('H:i');
            $displayTime = $currentTime->format('h:i A');

            $appointment = $appointments->get($timeKey);

            // Determine slot status
            if ($appointment) {
                $status = $appointment->status === 'completed' ? 'completed' : 'booked';
            } else {
                $status = 'available';
            }

            $slots[] = [
                'time' => $displayTime,
                'time_24' => $timeKey,
                'status' => $status,
                'appointment_id' => $appointment ? $appointment->id : null,
            ];

            $currentTime->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Determine doctor availability status
     */
    private function getDoctorAvailabilityStatus($availableSlots, $totalSlots)
    {
        if ($totalSlots === 0) {
            return 'unavailable';
        }

        $percentage = ($availableSlots / $totalSlots) * 100;

        if ($percentage >= 50) {
            return 'available';
        } elseif ($percentage > 0) {
            return 'busy';
        } else {
            return 'unavailable';
        }
    }

    /**
     * Get all unique specializations
     */
    public function getSpecializations()
    {
        return User::where('role', 'doctor')
            ->whereHas('doctorProfile.specialty')
            ->with('doctorProfile.specialty')
            ->get()
            ->pluck('doctorProfile.specialty.name')
            ->filter()
            ->unique()
            ->values();
    }
}
