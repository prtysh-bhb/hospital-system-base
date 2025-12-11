@extends('layouts.admin')

@section('title', 'Add Appointment')

@section('page-title', 'Add New Appointment')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 md:p-8">
            <form class="space-y-4 sm:space-y-6" action="{{ route('admin.store-appointment') }}" method="post"
                id="appointmentForm" enctype="multipart/form-data">
                @csrf

                <!-- Patient Selection -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                    <select id="select_patient" name="patient_id"
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
                        <option value="">Search or select patient... (or leave empty to create new)</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} -
                                {{ $patient->phone ?? '' }}</option>
                        @endforeach
                    </select>
                    <span id="patient_id_error" class="text-xs text-red-500 hidden"></span>
                </div>

                <!-- Patient Form -->
                <div id="patientForm"
                    class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-4 p-3 sm:p-4 bg-white rounded-lg border border-gray-200">
                    <div class="col-span-1 md:col-span-2">
                        <p class="text-xs sm:text-sm font-medium text-gray-800 mb-3">Patient Details</p>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">First Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" name="first_name" id="first_name" placeholder="Enter first name"
                            maxlength="25"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="first_name_error" class="text-xs text-red-500 hidden"></span>
                        <span class="text-xs text-gray-400"><span id="first_name_count">0</span>/25 characters</span>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Last Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" name="last_name" id="last_name" placeholder="Enter last name" maxlength="25"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="last_name_error" class="text-xs text-red-500 hidden"></span>
                        <span class="text-xs text-gray-400"><span id="last_name_count">0</span>/25 characters</span>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" placeholder="Enter email" maxlength="50"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="email_error" class="text-xs text-red-500 hidden"></span>
                        <span class="text-xs text-gray-400"><span id="email_count">0</span>/50 characters</span>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Phone <span
                                class="text-red-600">*</span></label>
                        <input type="tel" name="phone" id="phone" placeholder="Enter phone number" maxlength="15"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="phone_error" class="text-xs text-red-500 hidden"></span>
                        <span class="text-xs text-gray-400"><span id="phone_count">0</span>/15 characters</span>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date of Birth <span
                                class="text-red-600">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            max="{{ now()->subDay()->format('Y-m-d') }}"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="date_of_birth_error" class="text-xs text-red-500 hidden"></span>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Gender <span
                                class="text-red-600">*</span></label>
                        <select name="gender" id="gender"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span id="gender_error" class="text-xs text-red-500 hidden"></span>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2" maxlength="100"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                            placeholder="Enter full address..."></textarea>
                        <span id="address_error" class="text-xs text-red-500 hidden"></span>
                        <span class="text-xs text-gray-400"><span id="address_count">0</span>/100 characters</span>
                    </div>
                </div>

                <!-- Appointment Details Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mt-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Appointment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Doctor <span
                                    class="text-red-600">*</span></label>
                            <select id="doctor_select" name="doctor_id"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
                                <option value="">Search or select doctor...</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->first_name }} {{ $doctor->last_name }}
                                        @if ($doctor->doctorProfile && $doctor->doctorProfile->specialty)
                                            - {{ $doctor->doctorProfile->specialty->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <span id="doctor_id_error" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Date
                                <span class="text-red-600">*</span></label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                            <span id="appointment_date_error" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Time
                                <span class="text-red-600">*</span></label>
                            <select name="appointment_time" id="appointment_time"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                                <option value="">Select date first</option>
                            </select>
                            <span id="appointment_time_error" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Type
                                <span class="text-red-600">*</span></label>
                            <select id="type_select" name="appointment_type"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                                <option value="">Select type...</option>
                                <option value="consultation">Consultation</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="emergency">Emergency</option>
                                <option value="check_up">Check Up</option>
                            </select>
                            <span id="appointment_type_error" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status <span
                                    class="text-red-600">*</span></label>
                            <select name="status" id="status"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Reason for Visit
                                <span class="text-red-600">*</span></label>
                            <textarea name="reason_for_visit" id="reason_for_visit" rows="3" maxlength="1000"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Enter reason for visit..."></textarea>
                            <span id="reason_for_visit_error" class="text-xs text-red-500 hidden"></span>
                            <span class="text-xs text-gray-400"><span id="reason_count">0</span>/1000 characters</span>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="2" maxlength="500"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Enter any additional notes..."></textarea>
                            <span id="notes_error" class="text-xs text-red-500 hidden"></span>
                            <span class="text-xs text-gray-400"><span id="notes_count">0</span>/500 characters</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 sm:space-x-4 pt-4">
                    <a href="{{ route('admin.appointments') }}"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 text-center">Cancel</a>
                    <button type="submit" id="submitBtn"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">Create
                        Appointment</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize character counters
            function updateCharCount(field) {
                const fieldId = field.attr('id');
                const countSpan = $('#' + fieldId + '_count');
                if (countSpan.length) {
                    countSpan.text(field.val().length);
                }
            }

            // Update character counts on input
            $('#first_name, #last_name, #email, #phone, #address, #reason_for_visit, #notes').on('input',
                function() {
                    updateCharCount($(this));
                });

            // Toggle patient form based on selection
            function togglePatientForm() {
                var pid = $('#select_patient').val();
                if (pid && pid !== '') {
                    $('#patientForm').hide();
                    // Clear any existing errors in patient form
                    $('#patientForm input, #patientForm select, #patientForm textarea').each(function() {
                        clearFieldError($(this));
                    });
                } else {
                    $('#patientForm').show();
                }
            }

            // Initialize form visibility
            togglePatientForm();
            $('#select_patient').on('change', togglePatientForm);

            // Error handling functions
            function showFieldError(field, message) {
                const fieldId = field.attr('id');
                const errorSpanId = fieldId + '_error';
                let errorSpan = $('#' + errorSpanId);

                // If error span doesn't exist, try to find it
                if (!errorSpan.length) {
                    // Check if there's already an error span for this field
                    errorSpan = field.siblings('span[id$="_error"]').first();
                    if (!errorSpan.length) {
                        // Look for error span in the parent container
                        errorSpan = field.closest('div').find('span[id$="_error"]').first();
                    }
                }

                if (errorSpan.length) {
                    errorSpan.text(message).removeClass('hidden');
                    // Add error border to the field
                    field.addClass('border-red-500');

                    // Special handling for Select2
                    if (field.hasClass('select2-hidden-accessible')) {
                        field.next('.select2-container').find('.select2-selection').addClass('border-red-500');
                    }
                } else {
                    // Fallback: show as toast
                    toastr.error(message);
                }
            }

            function clearFieldError(field) {
                const fieldId = field.attr('id');
                let errorSpan = $('#' + fieldId + '_error');

                if (!errorSpan.length) {
                    // Try to find error span in siblings
                    errorSpan = field.siblings('span[id$="_error"]').first();
                }

                if (errorSpan.length) {
                    errorSpan.addClass('hidden').text('');
                }

                // Remove error border
                field.removeClass('border-red-500');

                // Special handling for Select2
                if (field.hasClass('select2-hidden-accessible')) {
                    field.next('.select2-container').find('.select2-selection').removeClass('border-red-500');
                }
            }

            // Clear errors on input
            $(document).on('input change', 'input, select, textarea', function() {
                clearFieldError($(this));
            });

            // Clear all errors
            function clearAllErrors() {
                $('input, select, textarea').each(function() {
                    clearFieldError($(this));
                });
            }

            // Function to handle backend errors
            function displayBackendErrors(errors) {
                clearAllErrors();

                Object.keys(errors).forEach(function(key) {
                    // Handle special cases where field names might not match exactly
                    let fieldName = key;
                    let field = $('[name="' + fieldName + '"]');

                    // If not found by name, try by id
                    if (!field.length) {
                        field = $('#' + fieldName);
                    }

                    // If still not found, try common variations
                    if (!field.length) {
                        if (key === 'patient_id') field = $('#select_patient');
                        else if (key === 'doctor_id') field = $('#doctor_select');
                        else if (key === 'appointment_type') field = $('#type_select');
                    }

                    if (field.length) {
                        showFieldError(field, errors[key][0]);
                    } else {
                        // If field not found, show as toast
                        toastr.error(errors[key][0]);
                    }
                });
            }

            // Form submission with AJAX
            $('#appointmentForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                clearAllErrors();

                // Show loading state
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Creating...'
                );

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            toastr.success(response.msg);
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('admin.appointments') }}";
                            }, 1500);
                        } else {
                            toastr.error(response.msg || 'Something went wrong');
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            // Validation errors from backend
                            let errors = xhr.responseJSON.errors;
                            if (errors) {
                                displayBackendErrors(errors);
                                // toastr.error('Please fix the validation errors.');
                            }
                        } else if (xhr.status === 400) {
                            // Other errors
                            let errorMsg = xhr.responseJSON.msg ||
                                'Something went wrong. Please try again.';
                            toastr.error(errorMsg);
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });

            // Select2 initialization (if needed)
            $('#select_patient, #doctor_select, #type_select').select2({
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select...';
                },
                allowClear: true,
                width: '100%'
            });

            // Load time slots function (from your existing code)
            function loadAvailableSlots() {
                const doctorId = $('#doctor_select').val();
                const date = $('#appointment_date').val();
                const timeSelect = $('#appointment_time');

                if (!doctorId || !date) {
                    timeSelect.html('<option value="">Select doctor and date first</option>');
                    return;
                }

                timeSelect.html('<option value="">Loading slots...</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.get-available-slots') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: date
                    },
                    success: function(response) {
                        timeSelect.prop('disabled', false);
                        if (response.success && response.slots && response.slots.length > 0) {
                            let options = '<option value="">Select Time</option>';
                            response.slots.forEach(function(slot) {
                                options += `<option value="${slot}">${slot}</option>`;
                            });
                            timeSelect.html(options);
                        } else {
                            timeSelect.html(
                                '<option value="">No slots available for this date</option>');
                        }
                    },
                    error: function() {
                        timeSelect.html('<option value="">Error loading slots</option>').prop(
                            'disabled', false);
                    }
                });
            }

            // Load slots when doctor or date changes
            $('#doctor_select, #appointment_date').on('change', function() {
                clearFieldError($(this));
                loadAvailableSlots();
            });
        });
    </script>
@endpush
