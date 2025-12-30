<form id="editAppointmentForm" class="space-y-4 sm:space-y-6" novalidate>
    @csrf
    <input type="hidden" id="appointment_id" name="appointment_id" value="{{ $appointment->id ?? '' }}">

    <!-- Patient Selection -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Patient <span
                class="text-red-600">*</span></label>
        <select id="edit_select_patient" name="patient_id"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option value="">Search or select patient...</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}"
                    {{ $appointment && $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </option>
            @endforeach
        </select>
        <p id="patient_error" class="text-xs text-red-500 mt-1 hidden"></p>
    </div>

    <!-- Doctor Selection -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Doctor <span
                class="text-red-600">*</span></label>
        <select id="edit_doctor_select" name="doctor_id"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option value="">Search or select doctor...</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}"
                    {{ $appointment && $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->first_name }} {{ $doctor->last_name }} -
                    {{ $doctor->doctorProfile->specialty->name }}
                </option>
            @endforeach
        </select>
        <p id="doctor_error" class="text-xs text-red-500 mt-1 hidden"></p>
    </div>

    <!-- Date & Time -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Date <span
                    class="text-red-600">*</span></label>
            <input type="date" id="edit_appointment_date" name="appointment_date"
                value="{{ $appointment ? $appointment->appointment_date->format('Y-m-d') : '' }}"
                min="{{ date('Y-m-d') }}"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <p id="date_error" class="text-xs text-red-500 mt-1 hidden"></p>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Time <span
                    class="text-red-600">*</span></label>
            <select id="edit_appointment_time" name="appointment_time"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
                <option value="">Select doctor and date first...</option>
                @if ($appointment)
                    <option value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}"
                        selected>
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }} (Current)
                    </option>
                @endif
            </select>
            <p id="time_slot_message" class="text-xs text-gray-500 mt-1"></p>
            <p id="time_error" class="text-xs text-red-500 mt-1 hidden"></p>
        </div>
    </div>

    <!-- Appointment Type -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Type <span
                class="text-red-600">*</span></label>
        <select id="edit_type_select" name="appointment_type"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option value="">Select type...</option>
            <option value="consultation"
                {{ $appointment && $appointment->appointment_type == 'consultation' ? 'selected' : '' }}>Consultation
            </option>
            <option value="follow_up"
                {{ $appointment && $appointment->appointment_type == 'follow_up' ? 'selected' : '' }}>Follow-up
            </option>
            <option value="emergency"
                {{ $appointment && $appointment->appointment_type == 'emergency' ? 'selected' : '' }}>Emergency
            </option>
            <option value="check_up"
                {{ $appointment && $appointment->appointment_type == 'check_up' ? 'selected' : '' }}>Check-up</option>
        </select>
        <p id="type_error" class="text-xs text-red-500 mt-1 hidden"></p>
    </div>

    <!-- Reason -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Reason for Visit <span
                class="text-red-600">*</span></label>
        <textarea id="edit_reason_for_visit" name="reason_for_visit"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            rows="4" placeholder="Enter reason for visit or symptoms">{{ $appointment ? $appointment->reason_for_visit : '' }}</textarea>
        <p id="reason_error" class="text-xs text-red-500 mt-1 hidden"></p>
    </div>

    <!-- Notes -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
        <textarea id="edit_notes" name="notes"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            rows="3" placeholder="Any additional information (optional)">{{ $appointment ? $appointment->notes : '' }}</textarea>
    </div>

    <!-- Patient Profile Fields (Conditional) -->
    @if (isset($formSettings['show_emergency_contact']) && $formSettings['show_emergency_contact'])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                <input type="text" id="edit_emergency_contact_name" name="emergency_contact_name"
                    value="{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->emergency_contact_name : '' }}"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    placeholder="Contact person name">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                <input type="text" id="edit_emergency_contact_phone" name="emergency_contact_phone"
                    value="{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->emergency_contact_phone : '' }}"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    placeholder="Contact phone number">
            </div>
        </div>
    @endif

    @if (isset($formSettings['show_blood_group']) && $formSettings['show_blood_group'])
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Blood Group</label>
            <select id="edit_blood_group" name="blood_group"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
                <option value="">Select blood group</option>
                @php $currentBloodGroup = $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->blood_group : ''; @endphp
                <option value="A+" {{ $currentBloodGroup == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ $currentBloodGroup == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ $currentBloodGroup == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ $currentBloodGroup == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ $currentBloodGroup == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ $currentBloodGroup == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ $currentBloodGroup == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ $currentBloodGroup == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
        </div>
    @endif

    @if (isset($formSettings['show_medical_history']) && $formSettings['show_medical_history'])
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Medical History</label>
            <textarea id="edit_medical_history" name="medical_history"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                rows="3" placeholder="Any past medical conditions, surgeries, etc.">{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->medical_history : '' }}</textarea>
        </div>
    @endif

    @if (isset($formSettings['show_current_medications']) && $formSettings['show_current_medications'])
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Current Medications</label>
            <textarea id="edit_current_medications" name="current_medications"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                rows="3" placeholder="List any medications currently taking">{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->current_medications : '' }}</textarea>
        </div>
    @endif

    @if (isset($formSettings['show_insurance_details']) && $formSettings['show_insurance_details'])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Insurance Provider</label>
                <input type="text" id="edit_insurance_provider" name="insurance_provider"
                    value="{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->insurance_provider : '' }}"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    placeholder="Insurance company name">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Insurance Number</label>
                <input type="text" id="edit_insurance_number" name="insurance_number"
                    value="{{ $appointment && $appointment->patient && $appointment->patient->patientProfile ? $appointment->patient->patientProfile->insurance_number : '' }}"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    placeholder="Policy/Member number">
            </div>
        </div>
    @endif

    <!-- Status -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status</label>
        <select id="edit_status" name="status"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option value="pending" {{ $appointment && $appointment->status == 'pending' ? 'selected' : '' }}>Pending
            </option>
            <option value="confirmed" {{ $appointment && $appointment->status == 'confirmed' ? 'selected' : '' }}>
                Confirmed</option>
            <option value="checked_in" {{ $appointment && $appointment->status == 'checked_in' ? 'selected' : '' }}>
                Checked In</option>
            <option value="in_progress" {{ $appointment && $appointment->status == 'in_progress' ? 'selected' : '' }}>
                In Progress</option>
            <option value="completed" {{ $appointment && $appointment->status == 'completed' ? 'selected' : '' }}>
                Completed</option>
            <option value="cancelled" {{ $appointment && $appointment->status == 'cancelled' ? 'selected' : '' }}>
                Cancelled</option>
            <option value="no_show" {{ $appointment && $appointment->status == 'no_show' ? 'selected' : '' }}>No Show
            </option>
        </select>
        <p id="status_conflict_message" class="text-xs text-red-500 mt-1 hidden"></p>
    </div>

    <!-- Cancellation Reason (shown only when status is cancelled) -->
    <div id="cancellation_reason_container" class="hidden">
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Cancellation Reason <span
                class="text-red-600">*</span></label>
        <textarea id="edit_cancellation_reason" name="cancellation_reason"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            rows="3" placeholder="Please provide a reason for cancellation">{{ $appointment ? $appointment->cancellation_reason : '' }}</textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <button type="button" onclick="closeEditModal()"
            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Cancel
        </button>
        <button type="submit" class="px-6 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
            Save Changes
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('editAppointmentForm');
        const doctorSelect = document.getElementById('edit_doctor_select');
        const dateInput = document.getElementById('edit_appointment_date');
        const timeSelect = document.getElementById('edit_appointment_time');
        const statusSelect = document.getElementById('edit_status');
        const submitBtn = form.querySelector('button[type="submit"]');
        const timeMessage = document.getElementById('time_slot_message');
        const statusConflictMessage = document.getElementById('status_conflict_message');
        const cancellationReasonContainer = document.getElementById('cancellation_reason_container');
        const cancellationReasonTextarea = document.getElementById('edit_cancellation_reason');
        const patientSelect = document.getElementById('edit_select_patient');
        const typeSelect = document.getElementById('edit_type_select');
        const reasonTextarea = document.getElementById('edit_reason_for_visit');

        // Original values to allow keeping the current slot
        const originalDoctorId = '{{ $appointment->doctor_id ?? '' }}';
        const originalDate = '{{ $appointment ? $appointment->appointment_date->format('Y-m-d') : '' }}';
        const originalTime =
            '{{ $appointment ? \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') : '' }}';
        const originalStatus = '{{ $appointment->status ?? '' }}';
        const appointmentId = '{{ $appointment->id ?? '' }}';

        // Track if slot is conflicted (for cancelled/no_show appointments)
        let hasSlotConflict = false;

        // Function to toggle cancellation reason field
        function toggleCancellationReason() {
            if (statusSelect.value === 'cancelled') {
                cancellationReasonContainer.classList.remove('hidden');
                cancellationReasonTextarea.setAttribute('required', 'required');
            } else {
                cancellationReasonContainer.classList.add('hidden');
                cancellationReasonTextarea.removeAttribute('required');
                cancellationReasonTextarea.value = ''; // Clear the value when hidden
            }
        }

        // Initialize cancellation reason visibility on page load
        toggleCancellationReason();

        // Check if a time is in the past (for today's date)
        function isTimePast(time24, date) {
            const today = new Date().toISOString().split('T')[0];
            if (date !== today) return false;

            const now = new Date();
            const [hours, minutes] = time24.split(':').map(Number);
            const slotTime = new Date();
            slotTime.setHours(hours, minutes, 0, 0);

            // Add 30 minutes buffer
            const bufferTime = new Date(now.getTime() + 30 * 60000);

            return slotTime <= bufferTime;
        }

        function fetchAvailableSlots() {
            const doctorId = doctorSelect.value;
            const date = dateInput.value;

            if (!doctorId || !date || doctorId === 'Search or select doctor...') {
                timeSelect.innerHTML = '<option value="">Select doctor and date first...</option>';
                timeMessage.textContent = '';
                return;
            }

            // Show loading
            timeSelect.innerHTML = '<option value="">Loading available slots...</option>';
            timeSelect.disabled = true;
            timeMessage.textContent = '';

            fetch(
                    `{{ route('admin.get-available-slots') }}?doctor_id=${doctorId}&date=${date}&exclude_appointment_id=${appointmentId}`
                )
                .then(response => response.json())
                .then(data => {
                    timeSelect.disabled = false;

                    if (data.success && data.slots.length > 0) {
                        let options = '<option value="">Select a time slot...</option>';

                        // Check if we should include original time
                        const isOriginalDoctorAndDate = (doctorId == originalDoctorId) && (date ==
                            originalDate);
                        let originalTimeIncluded = false;

                        // Check if original time is in the past (for today)
                        const isOriginalTimePast = isTimePast(originalTime, date);

                        data.slots.forEach(slot => {
                            const time24 = convertTo24Hour(slot);
                            const isOriginal = isOriginalDoctorAndDate && (time24 === originalTime);

                            if (isOriginal && !isOriginalTimePast) {
                                originalTimeIncluded = true;
                                options +=
                                    `<option value="${time24}" selected>${slot} (Current)</option>`;
                            } else {
                                options += `<option value="${time24}">${slot}</option>`;
                            }
                        });

                        // If original time not in available slots but same doctor/date, add it anyway ONLY if not past
                        if (isOriginalDoctorAndDate && !originalTimeIncluded && originalTime && !
                            isOriginalTimePast) {
                            const originalTime12 = convertTo12Hour(originalTime);
                            options =
                                `<option value="${originalTime}" selected>${originalTime12} (Current)</option>` +
                                options.replace('<option value="">Select a time slot...</option>', '');
                            options = '<option value="">Select a time slot...</option>' + options;
                        }

                        timeSelect.innerHTML = options;

                        // Show appropriate message
                        if (isOriginalTimePast && isOriginalDoctorAndDate) {
                            timeMessage.textContent =
                                `${data.slots.length} slot(s) available. Original time has passed - please select a new slot.`;
                            timeMessage.className = 'text-xs text-yellow-600 mt-1';
                        } else {
                            timeMessage.textContent = `${data.slots.length} slot(s) available`;
                            timeMessage.className = 'text-xs text-green-600 mt-1';
                        }

                        // If changing doctor/date and no selection, prompt user
                        if (!isOriginalDoctorAndDate && !timeSelect.value) {
                            timeMessage.textContent = 'Please select a new time slot';
                            timeMessage.className = 'text-xs text-yellow-600 mt-1';
                        }
                    } else {
                        // No slots available
                        let options = '<option value="">No slots available</option>';

                        // If same doctor/date, still allow original time ONLY if not past
                        const isOriginalDoctorAndDate = (doctorId == originalDoctorId) && (date ==
                            originalDate);
                        const isOriginalTimePast = isTimePast(originalTime, date);

                        if (isOriginalDoctorAndDate && originalTime && !isOriginalTimePast) {
                            const originalTime12 = convertTo12Hour(originalTime);
                            options =
                                `<option value="${originalTime}" selected>${originalTime12} (Current)</option>`;
                        }

                        timeSelect.innerHTML = options;

                        if (isOriginalTimePast) {
                            timeMessage.textContent =
                                'Original time has passed and no other slots are available';
                            timeMessage.className = 'text-xs text-red-500 mt-1';
                        } else {
                            timeMessage.textContent =
                                'Doctor is not available on this date or all slots are booked';
                            timeMessage.className = 'text-xs text-red-500 mt-1';
                        }
                    }

                    validateForm();
                })
                .catch(error => {
                    console.error('Error fetching slots:', error);
                    timeSelect.disabled = false;
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    timeMessage.textContent = 'Failed to load available slots. Please try again.';
                    timeMessage.className = 'text-xs text-red-500 mt-1';
                });
        }

        // Check if status change would cause a conflict
        function checkStatusConflict() {
            const currentStatus = statusSelect.value;
            const inactiveStatuses = ['cancelled', 'no_show'];
            const activeStatuses = ['pending', 'confirmed', 'checked_in', 'in_progress'];

            // If changing from inactive to active status, check for conflicts
            if (inactiveStatuses.includes(originalStatus) && activeStatuses.includes(currentStatus)) {
                // Need to check if the slot is still available
                const doctorId = doctorSelect.value;
                const date = dateInput.value;
                const time = timeSelect.value;

                if (!doctorId || !date || !time) return;

                // Also check if the time has passed
                if (isTimePast(time, date)) {
                    hasSlotConflict = true;
                    statusConflictMessage.textContent =
                        'Cannot change status: This appointment time has already passed.';
                    statusConflictMessage.classList.remove('hidden');
                    statusSelect.classList.add('border-red-500');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }

                fetch(`{{ route('admin.get-available-slots') }}?doctor_id=${doctorId}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const availableSlots = data.slots.map(slot => convertTo24Hour(slot));
                            const isAvailable = availableSlots.includes(time);

                            if (!isAvailable) {
                                hasSlotConflict = true;
                                statusConflictMessage.textContent =
                                    'Cannot change status: This time slot is now taken by another appointment.';
                                statusConflictMessage.classList.remove('hidden');
                                statusSelect.classList.add('border-red-500');
                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                hasSlotConflict = false;
                                statusConflictMessage.classList.add('hidden');
                                statusSelect.classList.remove('border-red-500');
                                validateForm();
                            }
                        }
                    })
                    .catch(error => console.error('Error checking slot:', error));
            } else {
                hasSlotConflict = false;
                statusConflictMessage.classList.add('hidden');
                statusSelect.classList.remove('border-red-500');
                validateForm();
            }
        }

        function validateForm() {
            const timeValue = timeSelect.value;
            const date = dateInput.value;

            // Check if selected time is in the past
            if (timeValue && isTimePast(timeValue, date)) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                timeMessage.textContent = 'Selected time has already passed. Please choose a future time.';
                timeMessage.className = 'text-xs text-red-500 mt-1';
                return;
            }

            if (!timeValue || hasSlotConflict) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function convertTo24Hour(time12h) {
            if (!time12h) return '';

            // Handle "09:00" (already 24h) or "09:00:00"
            if (!time12h.includes('AM') && !time12h.includes('PM')) {
                return time12h.substring(0, 5);
            }

            const [time, modifier] = time12h.split(' ');
            let [hours, minutes] = time.split(':');

            hours = parseInt(hours, 10);

            if (modifier === 'PM' && hours !== 12) {
                hours = hours + 12;
            } else if (modifier === 'AM' && hours === 12) {
                hours = 0;
            }

            return `${hours.toString().padStart(2, '0')}:${minutes}`;
        }

        function convertTo12Hour(time24h) {
            if (!time24h) return '';

            let [hours, minutes] = time24h.split(':');
            hours = parseInt(hours, 10);

            const modifier = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;

            return `${hours.toString().padStart(2, '0')}:${minutes} ${modifier}`;
        }

        // Comprehensive form validation
        function validateAllFields() {
            let isValid = true;

            // Validate Patient
            const patientError = document.getElementById('patient_error');
            if (!patientSelect.value || patientSelect.value === '') {
                patientSelect.classList.add('border-red-500');
                patientError.textContent = 'Please select a patient.';
                patientError.classList.remove('hidden');
                isValid = false;
            } else {
                patientSelect.classList.remove('border-red-500');
                patientError.classList.add('hidden');
            }

            // Validate Doctor
            const doctorError = document.getElementById('doctor_error');
            if (!doctorSelect.value || doctorSelect.value === '') {
                doctorSelect.classList.add('border-red-500');
                doctorError.textContent = 'Please select a doctor.';
                doctorError.classList.remove('hidden');
                isValid = false;
            } else {
                doctorSelect.classList.remove('border-red-500');
                doctorError.classList.add('hidden');
            }

            // Validate Date
            const dateError = document.getElementById('date_error');
            if (!dateInput.value) {
                dateInput.classList.add('border-red-500');
                dateError.textContent = 'Please select an appointment date.';
                dateError.classList.remove('hidden');
                isValid = false;
            } else {
                dateInput.classList.remove('border-red-500');
                dateError.classList.add('hidden');
            }

            // Validate Time
            const timeError = document.getElementById('time_error');
            if (!timeSelect.value || timeSelect.value === '') {
                timeSelect.classList.add('border-red-500');
                timeError.textContent = 'Please select an appointment time.';
                timeError.classList.remove('hidden');
                isValid = false;
            } else {
                timeSelect.classList.remove('border-red-500');
                timeError.classList.add('hidden');
            }

            // Validate Appointment Type
            const typeError = document.getElementById('type_error');
            if (!typeSelect.value || typeSelect.value === '') {
                typeSelect.classList.add('border-red-500');
                typeError.textContent = 'Please select an appointment type.';
                typeError.classList.remove('hidden');
                isValid = false;
            } else {
                typeSelect.classList.remove('border-red-500');
                typeError.classList.add('hidden');
            }

            // Validate Reason for Visit
            const reasonError = document.getElementById('reason_error');
            if (!reasonTextarea.value.trim()) {
                reasonTextarea.classList.add('border-red-500');
                reasonError.textContent = 'Please provide a reason for visit.';
                reasonError.classList.remove('hidden');
                isValid = false;
            } else {
                reasonTextarea.classList.remove('border-red-500');
                reasonError.classList.add('hidden');
            }

            // Validate Cancellation Reason if status is cancelled
            const cancellationReasonError = document.getElementById('cancellation_reason_error');
            if (statusSelect.value === 'cancelled') {
                const reason = cancellationReasonTextarea.value.trim();
                if (!reason) {
                    cancellationReasonTextarea.classList.add('border-red-500');
                    cancellationReasonError.textContent = 'Please provide a reason for cancellation.';
                    cancellationReasonError.classList.remove('hidden');
                    isValid = false;
                } else {
                    cancellationReasonTextarea.classList.remove('border-red-500');
                    cancellationReasonError.classList.add('hidden');
                }
            }

            return isValid;
        }

        // Clear validation error on field change
        function clearFieldValidationError(field, errorId) {
            field.classList.remove('border-red-500');
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('hidden');
            }
        }

        // Form submission handler
        form.addEventListener('submit', function(e) {
            if (!validateAllFields()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Event Listeners
        patientSelect.addEventListener('change', function() {
            clearFieldValidationError(patientSelect, 'patient_error');
        });

        doctorSelect.addEventListener('change', function() {
            fetchAvailableSlots();
            clearFieldValidationError(doctorSelect, 'doctor_error');
            // Recheck status conflict after slot change
            if (hasSlotConflict) {
                checkStatusConflict();
            }
        });

        dateInput.addEventListener('change', function() {
            fetchAvailableSlots();
            clearFieldValidationError(dateInput, 'date_error');
            // Recheck status conflict after date change
            if (hasSlotConflict) {
                checkStatusConflict();
            }
        });

        timeSelect.addEventListener('change', function() {
            validateForm();
            clearFieldValidationError(timeSelect, 'time_error');
            // Recheck status conflict after time change
            if (hasSlotConflict) {
                checkStatusConflict();
            }
        });

        typeSelect.addEventListener('change', function() {
            clearFieldValidationError(typeSelect, 'type_error');
        });

        reasonTextarea.addEventListener('input', function() {
            clearFieldValidationError(reasonTextarea, 'reason_error');
        });

        statusSelect.addEventListener('change', function() {
            toggleCancellationReason();
            checkStatusConflict();
        });

        // Validate cancellation reason on input
        cancellationReasonTextarea.addEventListener('input', function() {
            clearFieldValidationError(cancellationReasonTextarea, 'cancellation_reason_error');
        });

        // Initial load if values exist
        if (doctorSelect.value && dateInput.value && doctorSelect.value !== 'Search or select doctor...') {
            fetchAvailableSlots();
        }

        // Initial status conflict check for cancelled/no_show appointments
        if (['cancelled', 'no_show'].includes(originalStatus)) {
            checkStatusConflict();
        }
    })();

    if (xhr.status === 422 && xhr.responseJSON?.errors) {
        Object.entries(xhr.responseJSON.errors).forEach(([field, messages]) => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                showFieldError(input, messages[0]);
            }
        });
    }

    function showFieldError(field, message) {
        field.classList.add('border-red-500');
        field.classList.remove('border-gray-300');

        let errorEl = field.parentElement.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'field-error text-xs text-red-500 mt-1';
            field.parentElement.appendChild(errorEl);
        }
        errorEl.textContent = message;
    }

    function clearFieldError(field) {
        field.classList.remove('border-red-500');
        field.classList.add('border-gray-300');

        const errorEl = field.parentElement.querySelector('.field-error');
        if (errorEl) errorEl.remove();
    }
</script>
