@extends('layouts.admin')

@section('title', isset($doctor) ? 'Edit Doctor' : 'Add Doctor')

@section('page-title', isset($doctor) ? 'Edit Doctor' : 'Add New Doctor')

@section('content')
    <form action="{{ isset($doctor) ? route('admin.doctors.update', $doctor->user->id) : route('admin.doctors.store') }}"
        method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto" id="doctorForm">
        @csrf
        @if (isset($doctor))
            @method('PUT')
        @endif
        <!-- Personal Details -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Personal Details</h3>
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-700 rounded-lg mb-4">
                    <strong>Validation failed:</strong>
                    <ul class="mt-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">First Name <span
                            class="text-red-600">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $doctor->user->first_name ?? '') }}"
                        placeholder="Enter first name" minlength="2" maxlength="100" pattern="[a-zA-Z\s]+"
                        title="Name can only contain letters and spaces (minimum 2 characters)"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border {{ $errors->has('first_name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    <span id="first_name_error" class="text-red-600 text-sm mt-1 hidden"></span>
                    @error('first_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Last Name <span
                            class="text-red-600">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $doctor->user->last_name ?? '') }}"
                        placeholder="Enter last name" minlength="2" maxlength="100" pattern="[a-zA-Z\s]+"
                        title="Name can only contain letters and spaces (minimum 2 characters)"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border {{ $errors->has('last_name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    <span id="last_name_error" class="text-red-600 text-sm mt-1 hidden"></span>
                    @error('last_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span
                            class="text-red-600">*</span></label>
                    <select name="gender"
                        class="w-full px-4 py-2 border {{ $errors->has('gender') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $doctor->user->gender ?? '') == 'male' ? 'selected' : '' }}>
                            Male</option>
                        <option value="female"
                            {{ old('gender', $doctor->user->gender ?? '') == 'female' ? 'selected' : '' }}>
                            Female</option>
                        <option value="other"
                            {{ old('gender', $doctor->user->gender ?? '') == 'other' ? 'selected' : '' }}>
                            Other</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                            class="text-red-600">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $doctor->user->email ?? '') }}"
                        placeholder="Enter email address"
                        class="w-full px-4 py-2 border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    <span id="email_error" class="text-red-600 text-sm mt-1 hidden"></span>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span
                            class="text-red-600">*</span>
                        {{ !isset($doctor) ? '(will be used as password)' : '' }}</label>
                    <input type="tel" name="phone" id="phone"
                        value="{{ old('phone', $doctor->user->phone ?? '') }}" placeholder="9876543210" minlength="10"
                        maxlength="15" pattern="[0-9]{10,15}"
                        title="Phone number must be 10-15 digits only (cannot be all zeros)"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                        class="w-full px-4 py-2 border {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    <span id="phone_error" class="text-red-600 text-sm mt-1 hidden"></span>
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth <span
                            class="text-red-600">*</span></label>
                    <input type="date" name="date_of_birth"
                        value="{{ old('date_of_birth', isset($doctor) && $doctor->user->date_of_birth && strtotime($doctor->user->date_of_birth) ? date('Y-m-d', strtotime($doctor->user->date_of_birth)) : '') }}"
                        max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border {{ $errors->has('date_of_birth') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('date_of_birth')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if (isset($doctor))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span
                                class="text-red-600">*</span></label>
                        <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="active"
                                {{ old('status', $doctor->user->status ?? '') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive"
                                {{ old('status', $doctor->user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available for Booking <span
                                class="text-red-600">*</span></label>
                        <select name="available_for_booking"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="1"
                                {{ old('available_for_booking', $doctor->available_for_booking ?? 1) == 1 ? 'selected' : '' }}>
                                Yes</option>
                            <option value="0"
                                {{ old('available_for_booking', $doctor->available_for_booking ?? 1) == 0 ? 'selected' : '' }}>
                                No</option>
                        </select>
                    </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address <span
                            class="text-red-600">*</span></label>
                    <textarea name="address" rows="3" placeholder="Enter address" minlength="10" maxlength="500"
                        title="Address must be at least 10 characters"
                        class="w-full px-4 py-2 border {{ $errors->has('address') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">{{ old('address', $doctor->user->address ?? '') }}</textarea>
                    @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    @if (isset($doctor) && $doctor->user->profile_image)
                        <div class="mb-2">
                            <img src="{{ asset($doctor->user->profile_image) }}" alt="Current Photo"
                                class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Current photo</p>
                        </div>
                    @endif
                    <input type="file" name="profile_image" accept="image/jpeg,image/jpg,image/png"
                        class="w-full px-4 py-2 border {{ $errors->has('profile_image') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('profile_image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, JPEG, PNG (Max:
                        2MB){{ isset($doctor) ? '. Leave empty to keep current photo.' : '' }}</p>
                </div>

            </div>
        </div>

        <!-- Professional Details -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Professional Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialty <span
                            class="text-red-600">*</span></label>
                    <select name="specialty_id"
                        class="w-full px-4 py-2 border {{ $errors->has('specialty_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                        <option value="">Select Specialty</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}"
                                {{ old('specialty_id', $doctor->specialty_id ?? '') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('specialty_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Qualification <span
                            class="text-red-600">*</span></label>
                    <input type="text" name="qualification"
                        value="{{ old('qualification', $doctor->qualification ?? '') }}" placeholder="MBBS, MD"
                        minlength="2" maxlength="255"
                        class="w-full px-4 py-2 border {{ $errors->has('qualification') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('qualification')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years) <span
                            class="text-red-600">*</span></label>
                    <input type="number" name="experience_years"
                        value="{{ old('experience_years', $doctor->experience_years ?? '') }}" min="0"
                        max="70" placeholder="10"
                        class="w-full px-4 py-2 border {{ $errors->has('experience_years') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('experience_years')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Number <span
                            class="text-red-600">*</span></label>
                    <input type="text" name="license_number"
                        value="{{ old('license_number', $doctor->license_number ?? '') }}" placeholder="MCI12345"
                        minlength="3" maxlength="50"
                        class="w-full px-4 py-2 border {{ $errors->has('license_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('license_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Fee (₹) <span
                            class="text-red-600">*</span></label>
                    <input type="number" name="consultation_fee"
                        value="{{ old('consultation_fee', $doctor->consultation_fee ?? '') }}" min="0"
                        max="100000" step="0.01" placeholder="800"
                        oninput="if(this.value > 100000) this.value = 100000"
                        class="w-full px-4 py-2 border {{ $errors->has('consultation_fee') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('consultation_fee')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slot Duration (Minutes) <span
                            class="text-red-600">*</span></label>
                    <select name="slot_duration"
                        class="w-full px-4 py-2 border {{ $errors->has('slot_duration') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                        @php
                            $currentSlotDuration = isset($doctor)
                                ? $doctor->user->doctorSchedules->first()->slot_duration ?? 30
                                : 30;
                        @endphp
                        <option value="15" {{ old('slot_duration', $currentSlotDuration) == 15 ? 'selected' : '' }}>15
                        </option>
                        <option value="30" {{ old('slot_duration', $currentSlotDuration) == 30 ? 'selected' : '' }}>30
                        </option>
                        <option value="45" {{ old('slot_duration', $currentSlotDuration) == 45 ? 'selected' : '' }}>45
                        </option>
                        <option value="60" {{ old('slot_duration', $currentSlotDuration) == 60 ? 'selected' : '' }}>60
                        </option>
                    </select>
                    @error('slot_duration')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Languages Spoken</label>
                    <input type="text" name="languages" value="{{ old('languages', $doctor->bio ?? '') }}"
                        placeholder="English, Hindi, Marathi"
                        class="w-full px-4 py-2 border {{ $errors->has('languages') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                    @error('languages')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Availability Schedule -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Availability Schedule</h3>

            <div class="space-y-4">
                @php
                    $days = [
                        0 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                    ];

                    // Create a map of existing schedules if editing
                    $existingSchedules = [];
                    if (isset($doctor)) {
                        foreach ($doctor->user->doctorSchedules as $schedule) {
                            $existingSchedules[$schedule->day_of_week] = $schedule;
                        }
                    }
                @endphp

                @foreach ($days as $dayNum => $dayName)
                    @php
                        $existingSchedule = $existingSchedules[$dayNum] ?? null;
                        $isChecked = $existingSchedule && $existingSchedule->is_available;
                        $startTime = $existingSchedule
                            ? \Carbon\Carbon::parse($existingSchedule->start_time)->format('H:i')
                            : '09:00';
                        $endTime = $existingSchedule
                            ? \Carbon\Carbon::parse($existingSchedule->end_time)->format('H:i')
                            : ($dayNum == 6 || $dayNum == 0
                                ? '13:00'
                                : '17:00');
                    @endphp
                    <div class="flex items-center space-x-4">
                        <!-- Hidden input to ensure day is always sent, even when unchecked -->
                        <input type="hidden" name="schedules[{{ $dayNum }}][enabled]" value="0">
                        <input type="checkbox" name="schedules[{{ $dayNum }}][enabled]"
                            id="day{{ $dayNum }}" value="1" {{ $isChecked ? 'checked' : '' }}
                            class="w-5 h-5 text-sky-600 rounded">
                        <label for="day{{ $dayNum }}"
                            class="w-32 text-sm font-medium text-gray-700">{{ $dayName }}</label>
                        <input type="time" name="schedules[{{ $dayNum }}][start_time]"
                            value="{{ $startTime }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-gray-500">to</span>
                        <input type="time" name="schedules[{{ $dayNum }}][end_time]"
                            value="{{ $endTime }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 sm:space-x-4">
            <a href="{{ route('admin.doctors') }}"
                class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-center">Cancel</a>
            <button type="submit"
                class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">
                {{ isset($doctor) ? 'Update Doctor' : 'Add Doctor' }}
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('doctorForm');

            // Define required fields
            const requiredFields = [
                'first_name', 'last_name', 'gender', 'email', 'phone',
                'date_of_birth', 'address', 'specialty_id', 'qualification',
                'experience_years', 'license_number', 'consultation_fee', 'slot_duration'
            ];

            // Scroll to first error field if there are server-side errors
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }

            // Add blur validation for all required fields
            requiredFields.forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(this);
                    });

                    field.addEventListener('input', function() {
                        // Remove error styling on input
                        clearFieldError(this);
                    });
                }
            });

            // Form submit validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let firstInvalidField = null;

                requiredFields.forEach(fieldName => {
                    const field = form.querySelector(`[name="${fieldName}"]`);
                    if (field && !validateField(field)) {
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalidField.focus();
                    }
                }
            });

            function validateField(field) {
                const value = field.value.trim();
                let errorMessage = '';

                // Remove existing error message
                clearFieldError(field);

                // Check if field is empty
                if (!value) {
                    errorMessage = `${getFieldLabel(field)} is required`;
                }
                // Check minlength for text inputs
                else if (field.hasAttribute('minlength') && value.length < parseInt(field.getAttribute(
                        'minlength'))) {
                    errorMessage =
                        `${getFieldLabel(field)} must be at least ${field.getAttribute('minlength')} characters`;
                }
                // Check pattern for text inputs
                else if (field.hasAttribute('pattern') && value) {
                    const pattern = new RegExp(field.getAttribute('pattern'));
                    if (!pattern.test(value)) {
                        if (field.name.includes('name')) {
                            errorMessage = `${getFieldLabel(field)} can only contain letters and spaces`;
                        } else if (field.name === 'phone') {
                            errorMessage = 'Phone number must be 10-15 digits only';
                        }
                    }
                }
                // Check for all zeros in phone
                else if (field.name === 'phone' && /^0+$/.test(value)) {
                    errorMessage = 'Phone number cannot be all zeros';
                }
                // Check for select fields
                else if (field.tagName === 'SELECT' && (value === "" || value === null)) {
                    errorMessage = `Please select ${getFieldLabel(field)}`;
                }
                // Check email format
                else if (field.type === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        errorMessage = 'Please enter a valid email address';
                    }
                }
                // Check date of birth
                else if (field.name === 'date_of_birth' && value) {
                    const selectedDate = new Date(value);
                    const today = new Date();
                    if (selectedDate > today) {
                        errorMessage = 'Date of birth cannot be in the future';
                    }
                }

                if (errorMessage) {
                    // Add error styling
                    field.classList.remove('border-gray-300', 'focus:ring-sky-500');
                    field.classList.add('border-red-500', 'focus:ring-red-500');

                    // Add error message
                    const errorElement = document.createElement('p');
                    errorElement.className = 'error-message text-red-600 text-sm mt-1';
                    errorElement.textContent = errorMessage;
                    field.parentElement.appendChild(errorElement);

                    return false;
                } else {
                    // Remove error styling
                    clearFieldError(field);
                    return true;
                }
            }

            function clearFieldError(field) {
                field.classList.remove('border-red-500', 'focus:ring-red-500');
                field.classList.add('border-gray-300', 'focus:ring-sky-500');

                // Remove error message if exists
                const errorMsg = field.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }

            function getFieldLabel(field) {
                const label = field.parentElement.querySelector('label');
                if (label) {
                    return label.textContent.replace('*', '').replace('(will be used as password)', '').trim();
                }
                return field.name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
        });
    </script>
@endsection
