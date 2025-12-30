@extends('layouts.frontdesk')

@section('title', 'Add Appointment')

@section('page-title', 'Add New Appointment')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form id="appointmentForm" class="space-y-4 sm:space-y-6">
            @csrf
            <!-- Patient Selection -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Patient Information</h3>

                <div class="mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search Existing Patient</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" id="patientSearch" placeholder="Search by name, email, or phone..."
                            class="flex-1 px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <button type="button" id="searchBtn"
                            class="px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                            Search
                        </button>
                    </div>
                    <!-- Search Results -->
                    <div id="searchResults" class="mt-3 hidden"></div>
                </div>

                <!-- Selected Patient Info -->
                <div id="selectedPatientInfo" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                    <p class="text-sm font-medium text-green-800">Selected Patient: <span id="selectedPatientName"></span>
                    </p>
                    <input type="hidden" id="patient_id" name="patient_id">
                    <button type="button" onclick="clearPatientSelection()"
                        class="mt-2 text-xs text-red-600 hover:text-red-800">
                        Clear Selection
                    </button>
                </div>

                <!-- Patient Form - Always Visible -->
                <div id="patientForm"
                    class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-4 p-3 sm:p-4 bg-white rounded-lg border border-gray-200">
                    <div class="col-span-1 md:col-span-2">
                        <p class="text-xs sm:text-sm font-medium text-gray-800 mb-3">Patient Details</p>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">First Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" name="first_name" id="first_name" pattern="[A-Za-z\s]{2,100}"
                            title="First name should only contain letters and spaces (2-100 characters)"
                            placeholder="First name"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="first_name_error" class="text-xs text-red-500 hidden">First name should only contain
                            letters</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Last Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" name="last_name" id="last_name" pattern="[A-Za-z\s]{2,100}"
                            title="Last name should only contain letters and spaces (2-100 characters)"
                            placeholder="Last name"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="last_name_error" class="text-xs text-red-500 hidden">Last name should only contain
                            letters</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" placeholder="Enter email"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="email_error" class="text-xs text-red-500 hidden">Please enter a valid email address</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Phone <span
                                class="text-red-600">*</span></label>
                        <input type="tel" name="phone" id="phone" pattern="[0-9]{10,15}"
                            placeholder="Enter phone number" title="Phone number must be 10-15 digits only" maxlength="15"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                        <span id="phone_error" class="text-xs text-red-500 hidden">Phone must be 10-15 digits only</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date of Birth <span
                                class="text-red-600">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            max="{{ now()->subDay()->format('Y-m-d') }}"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
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
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                            placeholder="Enter full address..."></textarea>
                    </div>

                    <!-- Emergency Contact (Conditional) -->
                    @if (isset($formSettings['show_emergency_contact']) && $formSettings['show_emergency_contact'])
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Emergency Contact
                                Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Contact person name">
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Emergency Contact
                                Phone</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Contact phone number">
                        </div>
                    @endif

                    <!-- Blood Group (Conditional) -->
                    @if (isset($formSettings['show_blood_group']) && $formSettings['show_blood_group'])
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                            <select name="blood_group" id="blood_group"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                                <option value="">Select blood group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                    @endif

                    <!-- Medical History (Conditional) -->
                    @if (isset($formSettings['show_medical_history']) && $formSettings['show_medical_history'])
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Medical History</label>
                            <textarea name="medical_history" id="medical_history" rows="2"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Any past medical conditions, surgeries, etc."></textarea>
                        </div>
                    @endif

                    <!-- Current Medications (Conditional) -->
                    @if (isset($formSettings['show_current_medications']) && $formSettings['show_current_medications'])
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Current
                                Medications</label>
                            <textarea name="current_medications" id="current_medications" rows="2"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="List any medications currently taking"></textarea>
                        </div>
                    @endif

                    <!-- Insurance Details (Conditional) -->
                    @if (isset($formSettings['show_insurance_details']) && $formSettings['show_insurance_details'])
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Insurance
                                Provider</label>
                            <input type="text" name="insurance_provider" id="insurance_provider"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Insurance company name">
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Insurance Number</label>
                            <input type="text" name="insurance_number" id="insurance_number"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                                placeholder="Policy/Member number">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">

                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Appointment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Doctor <span
                                class="text-red-600">*</span></label>
                        <select name="doctor_id" id="doctor_id"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                            <option value="">Loading doctors...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Specialty</label>
                        <input type="text" id="specialty" readonly
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm sm:text-base">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Date <span
                                class="text-red-600">*</span></label>
                        <input type="date" name="appointment_date" id="appointment_date" min="{{ date('Y-m-d') }}"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Time <span
                                class="text-red-600">*</span></label>
                        <select name="appointment_time" id="appointment_time"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                            <option value="">Select date first</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Appointment Type <span
                                class="text-red-600">*</span></label>
                        <select name="appointment_type" id="appointment_type"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                            <option value="">Select Type</option>
                            <option value="consultation">Consultation</option>
                            <option value="follow_up">Follow-up</option>
                            <option value="emergency">Emergency</option>
                            <option value="check_up">General Checkup</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Consultation Fee</label>
                        <input type="text" id="consultation_fee" readonly
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm sm:text-base">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Reason for Visit <span
                                class="text-red-600">*</span></label>
                        <textarea name="reason_for_visit" id="reason_for_visit" rows="3"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                            placeholder="Enter reason for visit..."></textarea>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                            placeholder="Any additional notes or special requirements..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 sm:gap-4">
                <button type="button" onclick="window.location.href='{{ route('frontdesk.dashboard') }}'"
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm sm:text-base">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Appointment Booked Successfully!</h3>
                <p class="text-sm text-gray-500 mb-4">Appointment Number: <span id="appointmentNumber"
                        class="font-semibold"></span></p>
                <button onclick="window.location.href='{{ route('frontdesk.dashboard') }}'"
                    class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                    Back to Dashboard
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedPatientId = null;
        let doctorsData = [];
        let patients = [];

        // Load doctors on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDoctors();

            // Add event listeners
            document.getElementById('doctor_id').addEventListener('change', updateDoctorDetails);
            document.getElementById('appointment_date').addEventListener('change', loadAvailableSlots);
            document.getElementById('searchBtn').addEventListener('click', searchPatients);
            document.getElementById('patientSearch').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchPatients();
                }
            });

            // Add real-time validation listeners
            document.getElementById('first_name').addEventListener('input', validateFirstName);
            document.getElementById('last_name').addEventListener('input', validateLastName);
            document.getElementById('phone').addEventListener('input', validatePhone);
            document.getElementById('email').addEventListener('blur', validateEmail);

            // Form submission
            document.getElementById('appointmentForm').addEventListener('submit', handleFormSubmit);
        });

        // Validation functions
        function validateFirstName() {
            const input = document.getElementById('first_name');
            const error = document.getElementById('first_name_error');
            const value = input.value;

            // Remove any numbers or special characters (except spaces)
            input.value = value.replace(/[^A-Za-z\s]/g, '');

            if (value !== input.value) {
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else if (input.value.length >= 2) {
                error.classList.add('hidden');
                input.classList.remove('border-red-500');
            }
        }

        function validateLastName() {
            const input = document.getElementById('last_name');
            const error = document.getElementById('last_name_error');
            const value = input.value;

            // Remove any numbers or special characters (except spaces)
            input.value = value.replace(/[^A-Za-z\s]/g, '');

            if (value !== input.value) {
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else if (input.value.length >= 2) {
                error.classList.add('hidden');
                input.classList.remove('border-red-500');
            }
        }

        function validatePhone() {
            const input = document.getElementById('phone');
            const error = document.getElementById('phone_error');
            const value = input.value;

            // Remove any non-numeric characters
            input.value = value.replace(/[^0-9]/g, '');

            if (value !== input.value) {
                error.textContent = 'Phone must contain only numbers';
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else if (input.value.length > 0 && (input.value.length < 10 || input.value.length > 15)) {
                error.textContent = 'Phone must be 10-15 digits';
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else if (input.value.length >= 10 && input.value.length <= 15) {
                error.classList.add('hidden');
                input.classList.remove('border-red-500');
            }
        }

        function validateEmail() {
            const input = document.getElementById('email');
            const error = document.getElementById('email_error');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (input.value && !emailPattern.test(input.value)) {
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else {
                error.classList.add('hidden');
                input.classList.remove('border-red-500');
            }
        }

        // Load doctors from API
        function loadDoctors() {
            fetch('{{ route('frontdesk.add-appointment.doctors') }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        doctorsData = data.doctors;
                        const select = document.getElementById('doctor_id');
                        select.innerHTML = '<option value="">Choose a doctor</option>';

                        data.doctors.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = `${doctor.name} - ${doctor.specialty}`;
                            option.setAttribute('data-specialty', doctor.specialty);
                            option.setAttribute('data-fee', doctor.fee || 0);
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Failed to load doctors:', data.message);
                        document.getElementById('doctor_id').innerHTML =
                            '<option value="">Error loading doctors</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading doctors:', error);
                    document.getElementById('doctor_id').innerHTML =
                        '<option value="">Error loading doctors. Please refresh.</option>';
                });
        }

        // Update specialty and fee when doctor selected
        function updateDoctorDetails() {
            const select = document.getElementById('doctor_id');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('specialty').value = selectedOption.getAttribute('data-specialty') || '';
                const fee = selectedOption.getAttribute('data-fee') || 0;
                document.getElementById('consultation_fee').value = fee > 0 ? `$${parseFloat(fee).toFixed(2)}` : 'Free';
            } else {
                document.getElementById('specialty').value = '';
                document.getElementById('consultation_fee').value = '';
            }

            // Load available slots if date is selected
            const date = document.getElementById('appointment_date').value;
            if (date && selectedOption.value) {
                loadAvailableSlots();
            }
        }

        // Load available slots when date changes
        function loadAvailableSlots() {
            const doctorId = document.getElementById('doctor_id').value;
            const date = document.getElementById('appointment_date').value;
            const timeSelect = document.getElementById('appointment_time');

            if (!doctorId || !date) {
                timeSelect.innerHTML = '<option value="">Select doctor and date first</option>';
                return;
            }

            timeSelect.innerHTML = '<option value="">Loading slots...</option>';
            timeSelect.disabled = true;

            fetch(`{{ route('frontdesk.add-appointment.available-slots') }}?doctor_id=${doctorId}&date=${date}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    timeSelect.disabled = false;

                    if (data.success && data.slots && data.slots.length > 0) {
                        const selectedDate = new Date(date);
                        const today = new Date();
                        const isToday = selectedDate.toDateString() === today.toDateString();
                        const currentTime = today.getHours() * 60 + today.getMinutes();

                        timeSelect.innerHTML = '<option value="">Select Time</option>';
                        let availableSlots = 0;

                        data.slots.forEach(slot => {
                            let isPast = false;

                            if (isToday) {
                                // Parse time from slot (format: "HH:MM AM/PM" or "HH:MM")
                                const timeMatch = slot.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
                                if (timeMatch) {
                                    let hours = parseInt(timeMatch[1]);
                                    const minutes = parseInt(timeMatch[2]);
                                    const meridiem = timeMatch[3];

                                    // Convert to 24-hour format if AM/PM present
                                    if (meridiem) {
                                        if (meridiem.toUpperCase() === 'PM' && hours !== 12) {
                                            hours += 12;
                                        } else if (meridiem.toUpperCase() === 'AM' && hours === 12) {
                                            hours = 0;
                                        }
                                    }

                                    const slotTime = hours * 60 + minutes;
                                    isPast = slotTime <= currentTime;
                                }
                            }

                            if (!isPast) {
                                const option = document.createElement('option');
                                option.value = slot;
                                option.textContent = slot;
                                timeSelect.appendChild(option);
                                availableSlots++;
                            }
                        });

                        if (availableSlots === 0) {
                            timeSelect.innerHTML = '<option value="">No available slots remaining for today</option>';
                        }
                    } else {
                        timeSelect.innerHTML = '<option value="">No slots available for this date</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading slots:', error);
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    timeSelect.disabled = false;
                });
        }

        function searchPatients() {
            const search = document.getElementById('patientSearch').value.trim();
            if (search.length < 2) {
                alert('Please enter at least 2 characters');
                return;
            }

            fetch(`{{ route('frontdesk.add-appointment.search-patient') }}?search=${encodeURIComponent(search)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.patients);
                    } else {
                        console.error('Search failed:', data.message);
                        document.getElementById('searchResults').innerHTML =
                            '<p class="text-sm text-red-500 p-2">Search failed</p>';
                        document.getElementById('searchResults').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error searching patients:', error);
                    document.getElementById('searchResults').innerHTML =
                        '<p class="text-sm text-red-500 p-2">Error searching patients</p>';
                    document.getElementById('searchResults').classList.remove('hidden');
                });
        }

        function displaySearchResults(patientsData) {
            const resultsDiv = document.getElementById('searchResults');
            patients = patientsData || [];

            if (patients.length === 0) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 p-2">No patients found</p>';
                resultsDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = patients.map(patient => `
                <div class="p-3 border border-gray-200 rounded-lg mb-2 hover:bg-gray-50 cursor-pointer" onclick="selectPatient(${patient.id})">
                    <p class="text-sm font-medium">${patient.name || 'Unknown'}</p>
                    <p class="text-xs text-gray-500">${patient.email || 'No email'} | ${patient.phone || 'No phone'}</p>
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        }

        function selectPatient(id) {
            const patient = patients.find(p => p.id === id);
            if (!patient) return;

            selectedPatientId = id;
            document.getElementById('patient_id').value = id;
            document.getElementById('selectedPatientName').textContent = patient.name || 'Unknown Patient';
            document.getElementById('selectedPatientInfo').classList.remove('hidden');
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('patientSearch').value = '';

            // Split name into first and last name
            const nameParts = (patient.name || '').split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';

            // Fill patient details
            document.getElementById('first_name').value = firstName;
            document.getElementById('last_name').value = lastName;
            document.getElementById('email').value = patient.email || '';
            document.getElementById('phone').value = patient.phone || '';
            if (patient.date_of_birth) {
                const dob = patient.date_of_birth.split('T')[0]; // Extract only YYYY-MM-DD
                document.getElementById('date_of_birth').value = dob;
            }

            document.getElementById('gender').value = patient.gender || '';
            document.getElementById('address').value = patient.address || '';
            console.log(patient.address);

            // Hide patient form when existing patient is selected
            document.getElementById('patientForm').classList.add('hidden');

            // Disable fields when existing patient is selected
            const fieldsToDisable = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender', 'address'];
            fieldsToDisable.forEach(field => {
                const element = document.getElementById(field);
                if (element) element.disabled = true;
            });
        }

        function clearPatientSelection() {
            selectedPatientId = null;
            document.getElementById('patient_id').value = '';
            document.getElementById('selectedPatientInfo').classList.add('hidden');

            // Show patient form again
            document.getElementById('patientForm').classList.remove('hidden');

            // Clear and enable all fields
            const fieldsToClear = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender', 'address'];
            fieldsToClear.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.value = '';
                    element.disabled = false;
                }
            });
        }

        // Form submission
        function handleFormSubmit(e) {
            e.preventDefault();

            // Remove old error messages & borders
            clearAllErrors();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Booking...';

            let formData = new FormData(document.getElementById("appointmentForm"));

            fetch('{{ route('frontdesk.add-appointment.store') }}', {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    },
                    body: formData,
                })
                .then(async (response) => {
                    let data = await response.json();

                    // Validation error (422)
                    if (response.status === 422) {
                        showBackendErrors(data.errors);

                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Book Appointment';
                        return;
                    }

                    // SUCCESS
                    if (data.success) {
                        // toastr.success("Appointment booked successfully!");

                        // Open appointment success modal
                        document.getElementById('appointmentNumber').textContent = data.appointment_number;
                        document.getElementById('successModal').classList.remove('hidden');

                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Book Appointment';
                        return;
                    }

                    // Server-side failure
                    toastr.error(data.message || "Something went wrong");
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Book Appointment';
                })
                .catch((err) => {
                    console.error(err);
                    toastr.error("Server error! Please try again.");
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Book Appointment';
                });
        }

        // --------------------------------------------------------
        // Show Laravel Validation Errors Under Inputs
        // --------------------------------------------------------
        function showBackendErrors(errors) {
            Object.keys(errors).forEach((field) => {
                let input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add("border-red-500");

                    let span = document.createElement("span");
                    span.classList.add("text-red-500", "text-xs", "error-message");
                    span.innerText = errors[field][0];

                    input.insertAdjacentElement("afterend", span);

                    // Remove error & border when user types or changes field
                    input.addEventListener("input", removeError);
                    input.addEventListener("change", removeError);
                }
            });
        }

        // --------------------------------------------------------
        // Remove single error message & border
        // --------------------------------------------------------
        function removeError(e) {
            const input = e.target;
            input.classList.remove("border-red-500");

            const errorSpan = input.parentNode.querySelector(".error-message");
            if (errorSpan) errorSpan.remove();

            // Remove event listeners after clearing
            input.removeEventListener("input", removeError);
            input.removeEventListener("change", removeError);
        }

        // --------------------------------------------------------
        // Clear all errors at once
        // --------------------------------------------------------
        function clearAllErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        }
    </script>
@endpush
