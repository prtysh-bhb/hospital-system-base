<form id="editAppointmentForm" class="space-y-4 sm:space-y-6">
    @csrf
    <input type="hidden" id="appointment_id" name="appointment_id" value="{{ $appointment->id ?? '' }}">

    <!-- Patient Selection -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Patient <span
                class="text-red-600">*</span></label>
        <select id="edit_select_patient" name="patient_id"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option>Search or select patient...</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}"
                    {{ $appointment && $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Doctor Selection -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Doctor <span
                class="text-red-600">*</span></label>
        <select id="edit_doctor_select" name="doctor_id"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option>Search or select doctor...</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}"
                    {{ $appointment && $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->first_name }} {{ $doctor->last_name }} -
                    {{ $doctor->doctorProfile->specialty->name }}
                </option>
            @endforeach
        </select>
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
        </div>
    </div>

    <!-- Appointment Type -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Type <span
                class="text-red-600">*</span></label>
        <select id="edit_type_select" name="appointment_type"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option>Select type...</option>
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
    </div>

    <!-- Reason -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Reason for Visit <span
                class="text-red-600">*</span></label>
        <textarea id="edit_reason_for_visit" name="reason_for_visit"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            rows="4" placeholder="Enter reason for visit or symptoms">{{ $appointment ? $appointment->reason_for_visit : '' }}</textarea>
    </div>

    <!-- Notes -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
        <textarea id="edit_notes" name="notes"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            rows="3" placeholder="Any additional information (optional)">{{ $appointment ? $appointment->notes : '' }}</textarea>
    </div>

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

        // Original values to allow keeping the current slot
        const originalDoctorId = '{{ $appointment->doctor_id ?? '' }}';
        const originalDate = '{{ $appointment ? $appointment->appointment_date->format('Y-m-d') : '' }}';
        const originalTime =
            '{{ $appointment ? \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') : '' }}';
        const originalStatus = '{{ $appointment->status ?? '' }}';
        const appointmentId = '{{ $appointment->id ?? '' }}';

        // Track if slot is conflicted (for cancelled/no_show appointments)
        let hasSlotConflict = false;

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

        // Event Listeners
        doctorSelect.addEventListener('change', fetchAvailableSlots);
        dateInput.addEventListener('change', fetchAvailableSlots);
        timeSelect.addEventListener('change', validateForm);
        statusSelect.addEventListener('change', checkStatusConflict);

        // Initial load if values exist
        if (doctorSelect.value && dateInput.value && doctorSelect.value !== 'Search or select doctor...') {
            fetchAvailableSlots();
        }

        // Initial status conflict check for cancelled/no_show appointments
        if (['cancelled', 'no_show'].includes(originalStatus)) {
            checkStatusConflict();
        }
    })();
</script>
