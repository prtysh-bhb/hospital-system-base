<?php

namespace App\Http\Controllers\Doctor;

use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\DoctorLeave;
use Illuminate\Http\Request;
use App\Models\DoctorProfile;
use App\Events\NotifiyUserEvent;
use App\Enums\WhatsappTemplating;
use App\Http\Controllers\Controller;

class DoctorLeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $leaves = DoctorLeave::where('doctor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('doctor.leaves.create', compact('leaves'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'leave_type' => 'required|in:full_day,half_day,custom',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'half_day_slot' => 'nullable|required_if:leave_type,half_day|in:morning,evening',
            'start_time' => 'nullable|required_if:leave_type,custom|date_format:H:i',
            'end_time' => 'nullable|required_if:leave_type,custom|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
            'cancel_appointments' => 'nullable|boolean',
        ],[
            'start_date.after_or_equal' => 'The start date must be a date after or equal to today.',
            'end_date.after_or_equal' => 'The end date must be a date after or equal to start date.',
            'half_day_slot.required_if' => 'The half day slot is required when leave type is half day.',
            'start_time.required_if' => 'The start time is required when leave type is custom.',
            'end_time.required_if' => 'The end time is required when leave type is custom.',
            'end_time.after' => 'The end time must be a time after start time.',
            'reason.required' => 'Please provide a reason for your leave.',
        ]);

        if ($this->hasOverlappingLeave(
            $user->id,
            $request->input('start_date'),
            $request->input('end_date')
        )) {

            return response()->json([
                'success' => false,
                'type' => 'leave_conflict',
                'message' => 'You already have a leave applied for the selected date(s).',
                'errors' => [
                    'leave_dates' => ['You already have a leave applied for the selected date(s).'],
                ],
            ], 422);
        }
        $cancelAppointments = $request->boolean('cancel_appointments', false);

        $conflictingAppointments = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$request->input('start_date'), $request->input('end_date')])
            ->whereNotIn('status', ['cancelled', 'completed']);

        if ($conflictingAppointments->exists() && ! $cancelAppointments) {
            $count = $conflictingAppointments->count();

            return response()->json([
                'success' => false,
                'type' => 'appointment_conflict',
                'message' => "You have {$count} appointment(s) scheduled during the selected leave period.",
                'appointment_count' => $count,
            ], 409);

        }

        try {
            if ($cancelAppointments) {
                $appointments = Appointment::where('doctor_id', $user->id)
                    ->whereBetween('appointment_date', [$request->input('start_date'), $request->input('end_date')])
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->get();

                foreach ($appointments as $appointment) {
                    // Cancel each appointment
                    $appointment->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => 'Doctor on leave: ' . ($request->input('reason') ?? 'Leave approved'),
                    ]);

                    $patient = $appointment->patient;
                    $doctor = $appointment->doctor;

                    if ($patient && $patient->phone) {
                        $doctorName = 'Dr. ' . trim($doctor->first_name . ' ' . $doctor->last_name);
                        $patientName = trim($patient->first_name . ' ' . $patient->last_name);

                        // Get leave start and end date from request
                        $leaveStartDate = Carbon::parse($request->input('start_date'))->format('F j, Y');
                        $leaveEndDate = Carbon::parse($request->input('end_date'))->format('F j, Y');

                        // Booking link
                        $bookingLink = route('booking'); // Replace with your actual booking route

                        $components = [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['key' => 'name', 'type' => 'text', 'text' => $patientName],
                                    ['key' => 'doctor_name', 'type' => 'text', 'text' => $doctorName],
                                    ['key' => 'start_date', 'type' => 'text', 'text' => $leaveStartDate],
                                    ['key' => 'end_date', 'type' => 'text', 'text' => $leaveEndDate],
                                    ['key' => 'booking_link', 'type' => 'text', 'text' => $bookingLink],
                                ],
                            ],
                        ];

                        $params = [
                            'phone_number' => $patient->phone,
                            'template_name' => WhatsappTemplating::DOCTOR_ON_LEAVE->value,
                            'components' => $components,
                        ];

                        event(new NotifiyUserEvent($params));
                    }
                }
            }
            DoctorLeave::create([
                'doctor_id' => $user->id,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'leave_type' => $request->input('leave_type'),
                'half_day_slot' => $request->input('half_day_slot'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'reason' => $request->input('reason'),
                'status' => 'approved',
            ]);

            DoctorProfile::where('user_id', $user->id)->update(['available_for_booking' => false]);

            $message = 'Leave request submitted successfully.';
            if ($cancelAppointments) {
                $message = 'Leave request submitted and conflicting appointments have been cancelled.';
            }

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to submit leave request.'], 500);
        }
    }

    // check if a doctor has overlapping leave
    private function hasOverlappingLeave(
        int $doctorId,
        string $startDate,
        string $endDate
    ): bool {
        return DoctorLeave::where('doctor_id', $doctorId)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->exists();
    }
}
