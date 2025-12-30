<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Services\Admin\DoctoreServices;
use Illuminate\Http\Request;

class DoctorsController extends Controller
{
    protected DoctoreServices $doctoreServices;

    public function __construct(DoctoreServices $doctoreServices)
    {
        $this->doctoreServices = $doctoreServices;
    }

    public function index(Request $request)
    {
        $specialties = Specialty::where('status', 'active')->get();

        if ($request->ajax()) {
            $filters = [
                'search' => $request->input('search'),
                'specialty_id' => $request->input('specialty_id'),
                'status' => $request->input('status'),
            ];

            $doctors = $this->doctoreServices->getDoctors($filters);

            return view('admin.partials.doctor-cards', compact('doctors'))->render();
        }

        $doctors = $this->doctoreServices->getDoctors();

        return view('admin.doctors', compact('doctors', 'specialties'));
    }

    public function create(Request $request)
    {
        $specialties = Specialty::where('status', 'active')->get();

        if ($request->ajax()) {
            return view('admin.doctor-add', compact('specialties'));
        }

        return view('admin.doctor-add', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'username' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:50|unique:users,email',
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone', 'not_regex:/^0+$/'],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|min:10|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'specialty_id' => 'required|exists:specialties,id',
            'qualification' => 'required|string|min:2|max:255',
            'experience_years' => 'required|integer|min:0|max:70',
            'license_number' => 'required|string|min:3|max:50',
            'consultation_fee' => 'required|numeric|min:0|max:100000',
            'slot_duration' => 'required|integer|in:15,30,45,60',
            'languages' => 'nullable|string|max:255',
            'schedules' => 'nullable|array',
            'schedules.*.enabled' => 'boolean',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
        ], [
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'username.regex' => 'User name can only contain letters and spaces.',
            'username.min' => 'User name must be at least 2 characters.',
            'username.max' => 'User name cannot exceed 25 characters.',
            'first_name.max' => 'First name cannot exceed 25 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 25 characters.',
            'email.max' => 'Email cannot exceed 50 characters.',
            'phone.regex' => 'Phone number must be between 10-15 digits.',
            'phone.not_regex' => 'Phone number cannot be all zeros.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'address.min' => 'Address must be at least 10 characters.',
            'qualification.min' => 'Qualification must be at least 2 characters.',
            'license_number.min' => 'License number must be at least 3 characters.',
            'experience_years.max' => 'Experience years cannot exceed 70.',
            'consultation_fee.max' => 'Consultation fee cannot exceed ₹100,000.',
        ]);

        // Custom validation for schedule times
        if (! empty($validated['schedules'])) {
            foreach ($validated['schedules'] as $day => $schedule) {
                if (! empty($schedule['enabled']) && isset($schedule['start_time']) && isset($schedule['end_time'])) {
                    if (strtotime($schedule['end_time']) <= strtotime($schedule['start_time'])) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Validation failed',
                                'errors' => [
                                    "schedules.{$day}.end_time" => ['End time must be after start time for this day.'],
                                ],
                            ], 422);
                        }

                        return back()->withInput()->withErrors([
                            "schedules.{$day}.end_time" => 'End time must be after start time for this day.',
                        ]);
                    }
                }
            }
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/doctors'), $imageName);
            $validated['profile_image'] = 'uploads/doctors/'.$imageName;
        }

        try {
            $doctor = $this->doctoreServices->createDoctor($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Doctor created successfully!',
                    'doctor' => $doctor,
                    'redirect_url' => route('admin.doctors'),
                ]);
            }

            return redirect()->route('admin.doctors')
                ->with('success', 'Doctor created successfully');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add doctor. Please try again.',
                    'error' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Failed to add doctor. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        $doctor = $this->doctoreServices->getDoctorById($id);

        if (! $doctor) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
            }

            return redirect()->route('admin.doctors')
                ->with('error', 'Doctor not found.');
        }

        // Load schedules for the doctor
        $schedules = DoctorSchedule::where('doctor_id', $id)
            ->where('is_available', true)
            ->orderBy('day_of_week')
            ->get();

        // Get appointment statistics
        $totalAppointments = Appointment::where('doctor_id', $id)->count();
        $completedAppointments = Appointment::where('doctor_id', $id)
            ->where('status', 'completed')
            ->count();
        $upcomingAppointments = Appointment::where('doctor_id', $id)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->count();

        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'doctor' => [
                    'id' => $doctor->user->id,
                    'full_name' => $doctor->user->full_name,
                    'first_name' => $doctor->user->first_name,
                    'username' => $doctor->user->username,
                    'last_name' => $doctor->user->last_name,
                    'email' => $doctor->user->email,
                    'phone' => $doctor->user->phone,
                    'gender' => ucfirst($doctor->user->gender ?? 'N/A'),
                    'date_of_birth' => $doctor->user->date_of_birth ? $doctor->user->date_of_birth->format('M d, Y') : 'N/A',
                    'address' => $doctor->user->address ?? 'N/A',
                    'profile_image' => $doctor->user->profile_image ? asset($doctor->user->profile_image) : null,
                    'status' => $doctor->user->status,
                    'specialty' => $doctor->specialty->name ?? 'N/A',
                    'qualification' => $doctor->qualification ?? 'N/A',
                    'experience_years' => $doctor->experience_years ?? 0,
                    'consultation_fee' => number_format((float) $doctor->consultation_fee, 0),
                    'license_number' => $doctor->license_number ?? 'N/A',
                    'bio' => $doctor->bio ?? 'No bio available',
                    'available_for_booking' => $doctor->available_for_booking,
                    'languages' => $doctor->user->languages ?? 'N/A',
                    'created_at' => $doctor->user->created_at->format('M d, Y'),
                ],
                'schedules' => $schedules->map(function ($schedule) use ($dayNames) {
                    return [
                        'day' => $dayNames[$schedule->day_of_week] ?? 'Unknown',
                        'start_time' => date('h:i A', strtotime($schedule->start_time)),
                        'end_time' => date('h:i A', strtotime($schedule->end_time)),
                        'slot_duration' => $schedule->slot_duration,
                    ];
                }),
                'statistics' => [
                    'total_appointments' => $totalAppointments,
                    'completed_appointments' => $completedAppointments,
                    'upcoming_appointments' => $upcomingAppointments,
                ],
            ]);
        }

        return view('admin.doctor-view', compact('doctor', 'schedules', 'dayNames'));
    }

    public function edit(Request $request, $id)
    {
        $doctor = $this->doctoreServices->getDoctorById($id);

        if (! $doctor) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
            }

            return redirect()->route('admin.doctors')
                ->with('error', 'Doctor not found.');
        }

        $specialties = Specialty::where('status', 'active')->get();

        return view('admin.doctor-add', compact('doctor', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        // Build validation rules dynamically
        $rules = [
            'first_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:25|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:50|unique:users,email,'.$id,
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$id, 'not_regex:/^0+$/'],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|min:10|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'specialty_id' => 'required|exists:specialties,id',
            'qualification' => 'required|string|min:2|max:255',
            'experience_years' => 'required|integer|min:0|max:70',
            'license_number' => 'required|string|min:3|max:50',
            'consultation_fee' => 'required|numeric|min:0|max:100000',
            'slot_duration' => 'required|integer|in:15,30,45,60',
            'languages' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,suspended',
            'available_for_booking' => 'nullable|in:0,1',
            'schedules' => 'nullable|array',
            'schedules.*.enabled' => 'nullable|in:0,1',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
        ];
        $messages = [
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 25 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 25 characters.',
            'email.max' => 'Email cannot exceed 50 characters.',
            'phone.regex' => 'Phone number must be between 10-15 digits.',
            'phone.not_regex' => 'Phone number cannot be all zeros.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'address.min' => 'Address must be at least 10 characters.',
            'qualification.min' => 'Qualification must be at least 2 characters.',
            'license_number.min' => 'License number must be at least 3 characters.',
            'experience_years.max' => 'Experience years cannot exceed 70.',
            'consultation_fee.max' => 'Consultation fee cannot exceed ₹100,000.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ', $e->errors());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }

        // Custom validation for schedule times
        if (! empty($validated['schedules'])) {
            foreach ($validated['schedules'] as $day => $schedule) {
                if (! empty($schedule['enabled']) && $schedule['enabled'] == '1' && isset($schedule['start_time']) && isset($schedule['end_time'])) {
                    if (strtotime($schedule['end_time']) <= strtotime($schedule['start_time'])) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Validation failed',
                                'errors' => [
                                    "schedules.{$day}.end_time" => ['End time must be after start time for this day.'],
                                ],
                            ], 422);
                        }

                        return back()->withInput()->withErrors([
                            "schedules.{$day}.end_time" => 'End time must be after start time for this day.',
                        ]);
                    }
                }
            }
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/doctors'), $imageName);
            $validated['profile_image'] = 'uploads/doctors/'.$imageName;
        }

        try {
            $doctor = $this->doctoreServices->updateDoctor($id, $validated);

            \Log::info('Doctor updated successfully: '.$id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Doctor updated successfully!',
                    'doctor' => $doctor,
                    'redirect_url' => route('admin.doctors'),
                ]);
            }

            return redirect()->route('admin.doctors')
                ->with('success', 'Doctor updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Doctor update failed: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update doctor. Please try again.',
                    'error' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Failed to update doctor: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $User = $this->doctoreServices->deleteDoctor($id);

            if ($User) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Doctor deleted successfully!',
                    ]);
                }

                return redirect()->route('admin.doctors')
                    ->with('success', 'Doctor deleted successfully!');
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to delete doctor. Please try again.',
                    ], 422);
                }

                return redirect()->route('admin.doctors')
                    ->with('error', 'Failed to delete doctor. Please try again.');
            }
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the doctor.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.doctors')
                ->with('error', 'An error occurred while deleting the doctor.');
        }
    }
}
