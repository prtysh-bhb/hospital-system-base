@extends('layouts.doctor')

@section('title', 'Appointment Details')

@section('page-title', 'Appointment Details')

@section('header-back-button')
    <a href="{{ route('doctor.appointments') }}" class="text-gray-600 hover:text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
@endsection

@section('content')
    <div id="appointment-container" class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Loading State -->
        <div id="loading-state" class="lg:col-span-3 text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
            <p class="mt-4 text-gray-600">Loading appointment details...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="lg:col-span-3 text-center py-12 hidden">
            <div class="text-red-600 text-xl mb-4">⚠️</div>
            <p class="text-gray-600">Failed to load appointment details. Please try again.</p>
        </div>

        <!-- Left Column - Appointment & Patient Info -->
        <div id="main-content" class="lg:col-span-2 space-y-4 sm:space-y-6 hidden">
            <!-- Appointment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Appointment Information</h3>
                    <span id="appointment-status" class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium rounded-full">
                        Loading...
                    </span>
                </div>

                <!-- Status Change Section -->

                @if (
                    $appointment->status !== 'completed' ||
                        $appointment->status !== 'cancelled' ||
                        $appointment->status !== 'in_progress')
                    <div id="status-change-section" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Change Appointment Status</label>
                        <div class="flex gap-3">
                            <select id="status-select"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                                <option value="">Select status...</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="in_progress">In Progress</option>
                            </select>
                            <button id="update-status-btn"
                                class="px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium">
                                Update
                            </button>
                        </div>
                        <p id="status-message" class="mt-2 text-sm hidden"></p>
                    </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Appointment ID</p>
                        <p id="appointment-number" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Date & Time</p>
                        <p id="appointment-datetime" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Type</p>
                        <p id="appointment-type" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Duration</p>
                        <p id="appointment-duration" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Reason for Visit</p>
                        <p id="appointment-reason" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div id="symptoms-container" class="col-span-1 sm:col-span-2">
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Symptoms</p>
                        <p id="appointment-symptoms" class="text-sm sm:text-base font-medium text-gray-800">Not specified
                        </p>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Patient Information</h3>

                <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <img id="patient-avatar" src="" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full" alt="Patient">
                    <div class="flex-1">
                        <h4 id="patient-name" class="text-lg sm:text-xl font-semibold text-gray-800">-</h4>
                        <p id="patient-id" class="text-xs sm:text-sm text-gray-500 mb-2">-</p>
                        <div class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm">
                            <span id="patient-age" class="text-gray-600">-</span>
                            <span class="text-gray-400">•</span>
                            <span id="patient-gender" class="text-gray-600">-</span>
                            <span id="patient-blood-separator" class="text-gray-400">•</span>
                            <span id="patient-blood" class="text-gray-600">Blood Type: N/A</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Email</p>
                        <p id="patient-email" class="text-sm sm:text-base font-medium text-gray-800 break-all">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Phone</p>
                        <p id="patient-phone" class="text-sm sm:text-base font-medium text-gray-800">-</p>
                    </div>
                    <div id="patient-address-container" class="col-span-1 sm:col-span-2">
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Address</p>
                        <p id="patient-address" class="text-sm sm:text-base font-medium text-gray-800">Not provided</p>
                    </div>
                    <div id="patient-dob-container">
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Date of Birth</p>
                        <p id="patient-dob" class="text-sm sm:text-base font-medium text-gray-800">Not provided</p>
                    </div>
                    <div id="patient-emergency-container">
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Emergency Contact</p>
                        <p id="patient-emergency" class="text-sm sm:text-base font-medium text-gray-800">Not provided</p>
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div id="medical-history-section" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Medical History</h3>

                <div class="space-y-4">
                    <div id="allergies-container">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2">Allergies</p>
                        <div id="allergies-list" class="flex flex-wrap gap-2">
                            <span class="text-xs sm:text-sm text-gray-500 italic">No known allergies</span>
                        </div>
                    </div>

                    <div id="conditions-container">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2">Chronic Conditions</p>
                        <div id="conditions-list" class="flex flex-wrap gap-2">
                            <span class="text-xs sm:text-sm text-gray-500 italic">No chronic conditions recorded</span>
                        </div>
                    </div>

                    <div id="medications-container">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2">Current Medications</p>
                        <ul id="medications-list"
                            class="list-disc list-inside space-y-1 text-xs sm:text-sm text-gray-600">
                            <li class="text-gray-500 italic list-none">No current medications</li>
                        </ul>
                    </div>

                    <div id="previous-visits-container">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2">Previous Visits</p>
                        <div id="previous-visits-list" class="space-y-2">
                            <p class="text-xs sm:text-sm text-gray-500 italic">No previous visits recorded</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultation Notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Consultation Notes</h3>
                    <button id="save-notes-btn"
                        class="px-3 sm:px-4 py-2 bg-sky-600 text-white text-xs sm:text-sm rounded-lg hover:bg-sky-700">
                        Save Notes
                    </button>
                </div>
                <textarea id="consultation-notes"
                    class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm sm:text-base"
                    rows="6" placeholder="Enter consultation notes, diagnosis, and treatment plan..."></textarea>
                <p id="notes-message" class="mt-2 text-sm hidden"></p>
            </div>

            <!-- Prescription -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Add New Prescription</h3>
                    <button id="add-medication-btn"
                        class="px-3 sm:px-4 py-2 bg-sky-600 text-white text-xs sm:text-sm rounded-lg hover:bg-sky-700">
                        + Add Medication
                    </button>
                </div>

                <div id="medications-prescription-list" class="space-y-3 mb-4 min-h-[50px]">
                    <p class="text-sm text-gray-500 italic">No medications added yet. Click "Add Medication" to start.</p>
                </div>

                <button id="save-prescription-btn"
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    Save Prescription
                </button>
                <p id="prescription-message" class="mt-2 text-sm hidden"></p>
            </div>

            <!-- All Prescriptions -->
            <div id="existing-prescriptions-section"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Prescriptions History</h3>
                <div id="existing-prescriptions-list" class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                    <p class="text-sm text-gray-500 italic">No prescriptions available</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">

                @if ($appointment->status !== 'completed')
                    <!-- Mark as Completed Button (only show when NOT completed) -->
                    <button id="complete-appointment-btn"
                        class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm sm:text-base">
                        Mark as Completed
                    </button>
                @endif

                <!-- Back to Appointments Button (full width when completed) -->
                <button onclick="window.location.href='{{ route('doctor.appointments') }}'"
                    class="{{ $appointment->status == 'completed' ? 'flex-1' : '' }} px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm sm:text-base">
                    Back to Appointments
                </button>

            </div>
        </div>

        <!-- Right Column - Quick Actions -->
        <div id="sidebar-content" class="space-y-4 sm:space-y-6 hidden">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button onclick="window.location.href='{{ route('doctor.appointments') }}'"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100 text-xs sm:text-sm font-medium text-left">
                        View All Appointments
                    </button>
                    <button onclick="document.getElementById('consultation-notes').scrollIntoView({ behavior: 'smooth' })"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 text-xs sm:text-sm font-medium text-left">
                        Jump to Notes
                    </button>
                    <button onclick="document.getElementById('add-medication-btn').click()"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 text-xs sm:text-sm font-medium text-left">
                        Add Prescription
                    </button>
                    <button onclick="document.getElementById('followup-date').scrollIntoView({ behavior: 'smooth' })"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 text-xs sm:text-sm font-medium text-left">
                        Schedule Follow-up
                    </button>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Vital Signs</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-xs sm:text-sm text-gray-600">Blood Pressure</span>
                        <input type="text" id="vital-bp" placeholder="120/80"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-xs sm:text-sm text-gray-600">Heart Rate (bpm)</span>
                        <input type="text" id="vital-hr" placeholder="72"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-xs sm:text-sm text-gray-600">Temperature (°F)</span>
                        <input type="text" id="vital-temp" placeholder="98.6"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-xs sm:text-sm text-gray-600">Oxygen Sat (%)</span>
                        <input type="text" id="vital-o2" placeholder="98"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-xs sm:text-sm text-gray-600">Weight (lbs)</span>
                        <input type="text" id="vital-weight" placeholder="180"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs sm:text-sm text-gray-600">Height (in)</span>
                        <input type="text" id="vital-height" placeholder="70"
                            class="w-20 sm:w-24 px-2 py-1 text-xs sm:text-sm border border-gray-300 rounded text-right">
                    </div>
                </div>
                <button id="save-vitals-btn"
                    class="w-full mt-4 px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-xs sm:text-sm">
                    Save Vitals
                </button>
                <p id="vitals-message" class="mt-2 text-sm hidden"></p>
            </div>

            <!-- Follow-up -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Schedule Follow-up</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="date" id="followup-date"
                            class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Time</label>
                        <select id="followup-time"
                            class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base">
                            <option value="">Select a date first</option>
                        </select>
                        <p id="followup-time-loading" class="text-xs text-gray-500 mt-1 hidden">Loading available slots...
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                        <input type="text" id="followup-reason"
                            class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base"
                            placeholder="Follow-up reason">
                    </div>
                    <button id="schedule-followup-btn"
                        class="w-full px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-xs sm:text-sm">
                        Schedule Follow-up
                    </button>
                    <p id="followup-message" class="text-sm hidden"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Appointment Confirmation Modal -->
    <div id="complete-appointment-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Complete Appointment</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to mark this appointment as completed? This
                    action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button id="cancel-complete-btn"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button id="confirm-complete-btn"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Add Medication Modal -->
    <div id="medication-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Add Prescription</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
                    <input type="text" id="med-diagnosis"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        placeholder="e.g., Hypertension">
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medication Name</label>
                    <input type="text" id="med-name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        placeholder="e.g., Paracetamol">
                    <ul id="med-suggestions"
                        class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto hidden">
                    </ul>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                    <input type="text" id="med-dosage"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="e.g., 25mg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                    <input type="text" id="med-frequency"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        placeholder="e.g., Twice daily">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <input type="text" id="med-duration"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="e.g., 30 days">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="text" id="med-quantity"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="e.g., 60 tablets">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button id="confirm-add-medication"
                    class="flex-1 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">Add</button>
                <button id="cancel-add-medication"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const medInput = document.getElementById('med-name');
            const listBox = document.getElementById('med-suggestions');
            const dosageInput = document.getElementById('med-dosage');

            let timer = null;
            let suggestions = [];
            let activeIndex = -1;

            // ================= FETCH MEDICINES =================
            medInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();

                if (q.length < 3) {
                    hideList();
                    return;
                }

                clearTimeout(timer);

                timer = setTimeout(() => {
                    fetch(
                            `https://rxnav.nlm.nih.gov/REST/approximateTerm.json?term=${encodeURIComponent(q)}&maxEntries=30`
                        )
                        .then(res => res.json())
                        .then(data => {
                            const raw = data.approximateGroup?.candidate || [];

                            // FILTER: undefined / empty / mismatch
                            suggestions = raw.filter(item =>
                                item &&
                                item.name &&
                                item.name.toLowerCase().includes(q)
                            );

                            renderList();
                        })
                        .catch(err => console.error(err));
                }, 300);
            });

            // ================= RENDER LIST =================
            function renderList() {
                listBox.innerHTML = '';
                activeIndex = -1;

                if (!suggestions.length) {
                    hideList();
                    return;
                }

                suggestions.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.textContent = item.name;
                    li.className =
                        'px-3 py-2 text-sm cursor-pointer hover:bg-sky-100';

                    li.addEventListener('click', () => selectItem(index));
                    listBox.appendChild(li);
                });

                listBox.classList.remove('hidden');
            }

            // ================= KEYBOARD NAVIGATION =================
            medInput.addEventListener('keydown', function(e) {
                if (listBox.classList.contains('hidden')) return;

                const items = listBox.querySelectorAll('li');

                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, items.length - 1);
                    updateActive(items);
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    updateActive(items);
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeIndex >= 0) {
                        selectItem(activeIndex);
                    }
                }

                if (e.key === 'Escape') {
                    hideList();
                }
            });

            // ================= UPDATE ACTIVE + SCROLL =================
            function updateActive(items) {
                items.forEach((li, i) => {
                    li.classList.toggle('bg-sky-200', i === activeIndex);
                });

                // AUTO SCROLL INTO VIEW
                if (items[activeIndex]) {
                    items[activeIndex].scrollIntoView({
                        block: 'nearest',
                        behavior: 'smooth'
                    });
                }
            }

            // ================= SELECT ITEM =================
            function selectItem(index) {
                const name = suggestions[index].name;
                medInput.value = name;
                hideList();

                // Auto dosage
                const dose = name.match(/\d+\s?(mg|ml|mcg)/i);
                if (dose) {
                    dosageInput.value = dose[0];
                }
            }

            // ================= HIDE LIST =================
            function hideList() {
                listBox.classList.add('hidden');
                listBox.innerHTML = '';
                suggestions = [];
                activeIndex = -1;
            }

            // ================= CLICK OUTSIDE =================
            document.addEventListener('click', function(e) {
                if (!medInput.contains(e.target) && !listBox.contains(e.target)) {
                    hideList();
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const appointmentId = {{ $appointment->id }};
                let appointmentData = null;
                let medications = [];

                // Load appointment details
                loadAppointmentDetails();

                function loadAppointmentDetails() {
                    fetch(`/doctor/appointments/${appointmentId}/details-json`)
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                appointmentData = result.data;
                                populateAppointmentData(result.data);
                                document.getElementById('loading-state').classList.add('hidden');
                                document.getElementById('main-content').classList.remove('hidden');
                                document.getElementById('sidebar-content').classList.remove('hidden');
                            } else {
                                showError();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showError();
                        });
                }

                function showError() {
                    document.getElementById('loading-state').classList.add('hidden');
                    document.getElementById('error-state').classList.remove('hidden');
                }

                function populateAppointmentData(data) {
                    // Appointment Info
                    const statusColors = {
                        'pending': 'bg-yellow-100 text-yellow-700',
                        'confirmed': 'bg-blue-100 text-blue-700',
                        'scheduled': 'bg-green-100 text-green-700',
                        'in_progress': 'bg-purple-100 text-purple-700',
                        'completed': 'bg-gray-100 text-gray-700',
                        'cancelled': 'bg-red-100 text-red-700'
                    };

                    document.getElementById('appointment-status').className =
                        `px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium rounded-full ${statusColors[data.appointment.status] || 'bg-gray-100 text-gray-700'}`;
                    document.getElementById('appointment-status').textContent = data.appointment.status.charAt(0)
                        .toUpperCase() + data.appointment.status.slice(1).replace('_', ' ');

                    // Show/hide status change section based on current status
                    const currentStatus = data.appointment.status;
                    if (currentStatus !== 'completed' && currentStatus !== 'cancelled') {
                        document.getElementById('status-change-section').classList.remove('hidden');
                        updateStatusOptions(currentStatus);
                    } else {
                        document.getElementById('status-change-section').classList.add('hidden');
                    }

                    document.getElementById('appointment-number').textContent = data.appointment.appointment_number;
                    document.getElementById('appointment-datetime').textContent =
                        `${data.appointment.date} • ${data.appointment.time}`;
                    document.getElementById('appointment-type').textContent = data.appointment.type;
                    document.getElementById('appointment-duration').textContent =
                        `${data.appointment.duration} minutes`;
                    document.getElementById('appointment-reason').textContent = data.appointment.reason ||
                        'Not specified';

                    document.getElementById('appointment-symptoms').textContent = data.appointment.symptoms ||
                        'Not specified';

                    // Patient Info
                    const patientName = data.patient.name;
                    const dob = data.patient.date_of_birth;
                    console.log(dob);

                    function calculateAge(dob) {
                        const birth = new Date(Date.parse(dob));
                        const today = new Date();
                        let age = today.getFullYear() - birth.getFullYear();
                        if (
                            today.getMonth() < birth.getMonth() ||
                            (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())
                        ) {
                            age--;
                        }
                        return age;
                    }
                    const age = dob ? calculateAge(dob) : 'N/A';

                    document.getElementById('patient-avatar').src = data.patient.profile_image ||
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(patientName)}&background=10b981&color=fff`;
                    document.getElementById('patient-name').textContent = patientName;
                    document.getElementById('patient-id').textContent = data.patient.patient_number;
                    document.getElementById('patient-age').textContent = `Age: ${age}`;
                    document.getElementById('patient-gender').textContent = data.patient.gender;
                    document.getElementById('patient-blood').textContent =
                        `Blood Type: ${data.patient.blood_group || 'N/A'}`;
                    document.getElementById('patient-email').textContent = data.patient.email || 'N/A';
                    document.getElementById('patient-phone').textContent = data.patient.phone || 'N/A';
                    document.getElementById('patient-address').textContent = data.patient.address || 'Not provided';
                    document.getElementById('patient-dob').textContent = data.patient.date_of_birth || 'Not provided';
                    document.getElementById('patient-emergency').textContent = data.patient.emergency_contact_name ?
                        `${data.patient.emergency_contact_name}: ${data.patient.emergency_contact_phone || 'N/A'}` :
                        'Not provided';

                    // Medical History - Always show sections
                    const allergiesList = document.getElementById('allergies-list');
                    if (data.patient.allergies && data.patient.allergies.length > 0) {
                        allergiesList.innerHTML = data.patient.allergies.map(allergy =>
                            `<span class="px-2 sm:px-3 py-1 bg-red-100 text-red-700 text-xs sm:text-sm rounded-full">${allergy}</span>`
                        ).join('');
                    } else {
                        allergiesList.innerHTML =
                            '<span class="text-xs sm:text-sm text-gray-500 italic">No known allergies</span>';
                    }

                    const conditionsList = document.getElementById('conditions-list');
                    if (data.patient.medical_history && data.patient.medical_history.length > 0) {
                        conditionsList.innerHTML = data.patient.medical_history.map(condition =>
                            `<span class="px-2 sm:px-3 py-1 bg-orange-100 text-orange-700 text-xs sm:text-sm rounded-full">${condition}</span>`
                        ).join('');
                    } else {
                        conditionsList.innerHTML =
                            '<span class="text-xs sm:text-sm text-gray-500 italic">No chronic conditions recorded</span>';
                    }

                    const medicationsList = document.getElementById('medications-list');
                    if (data.patient.current_medications && data.patient.current_medications.length > 0) {
                        medicationsList.innerHTML = data.patient.current_medications.map(med =>
                            `<li>${med}</li>`
                        ).join('');
                    } else {
                        medicationsList.innerHTML =
                            '<li class="text-gray-500 italic list-none">No current medications</li>';
                    }

                    const visitsList = document.getElementById('previous-visits-list');
                    if (data.patient.previous_appointments && data.patient.previous_appointments.length > 0) {
                        visitsList.innerHTML = data.patient.previous_appointments.map(visit =>
                            `<div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs sm:text-sm font-medium text-gray-800">${visit.date} - ${visit.reason}</p>
                    <p class="text-xs text-gray-600 mt-1">${visit.doctor} - ${visit.specialty}</p>
                </div>`
                        ).join('');
                    } else {
                        visitsList.innerHTML =
                            '<p class="text-xs sm:text-sm text-gray-500 italic">No previous visits recorded</p>';
                    }

                    // Consultation Notes
                    if (data.appointment.notes) {
                        document.getElementById('consultation-notes').value = data.appointment.notes;
                    }

                    // Existing Prescriptions - Always show section
                    const prescriptionsList = document.getElementById('existing-prescriptions-list');
                    prescriptionsList.classList.remove('max-h-[600px]', 'overflow-y-auto');
                    if (data.prescriptions && data.prescriptions.length > 0) {
                        // Add scroll if more than 2 prescriptions
                        if (data.prescriptions.length > 2) {
                            prescriptionsList.classList.add('max-h-[600px]', 'overflow-y-auto');
                        }
                        prescriptionsList.innerHTML = data.prescriptions.map(presc => {
                            const medsHtml = presc.medications.map(med =>
                                `<li><strong>${med.name} ${med.dosage}</strong> - ${med.frequency}, ${med.duration}${med.quantity ? `, Qty: ${med.quantity}` : ''}</li>`
                            ).join('');

                            return `<div class="p-4 border-2 border-gray-200 rounded-lg hover:border-sky-300 transition-colors bg-gradient-to-br from-white to-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-sm font-bold text-sky-700">${presc.prescription_number}</span>
                            ${presc.diagnosis ? `<p class="text-sm font-semibold text-gray-800 mt-1">Diagnosis: ${presc.diagnosis}</p>` : ''}
                        </div>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${presc.created_at}</span>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Medications:</p>
                        <ul class="text-sm text-gray-700 space-y-2">${medsHtml}</ul>
                    </div>
                    ${presc.instructions ? `<p class="text-sm text-gray-600 mt-3 italic bg-blue-50 p-2 rounded"><strong>Instructions:</strong> ${presc.instructions}</p>` : ''}
                </div>`;
                        }).join('');

                    } else {
                        prescriptionsList.innerHTML =
                            '<p class="text-sm text-gray-500 italic">No prescriptions available</p>';
                    }

                    // Set minimum date for follow-up
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    document.getElementById('followup-date').min = tomorrow.toISOString().split('T')[0];
                }

                // Save Consultation Notes
                document.getElementById('save-notes-btn').addEventListener('click', function() {
                    const notes = document.getElementById('consultation-notes').value;
                    const messageEl = document.getElementById('notes-message');

                    if (!notes.trim()) {
                        showMessage(messageEl, 'Please enter consultation notes', 'error');
                        return;
                    }

                    fetch(`/doctor/appointments/${appointmentId}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                notes
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                showMessage(messageEl, result.msg, 'success');
                            } else {
                                showMessage(messageEl, result.msg || 'Failed to save notes', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(messageEl, 'An error occurred', 'error');
                        });
                });

                // Medication Modal
                document.getElementById('add-medication-btn').addEventListener('click', function() {
                    document.getElementById('medication-modal').classList.remove('hidden');
                    // Clear inputs
                    document.getElementById('med-diagnosis').value = '';
                    document.getElementById('med-name').value = '';
                    document.getElementById('med-dosage').value = '';
                    document.getElementById('med-frequency').value = '';
                    document.getElementById('med-duration').value = '';
                    document.getElementById('med-quantity').value = '';
                });

                document.getElementById('cancel-add-medication').addEventListener('click', function() {
                    document.getElementById('medication-modal').classList.add('hidden');
                });

                document.getElementById('confirm-add-medication').addEventListener('click', function() {
                    const diagnosis = document.getElementById('med-diagnosis').value.trim();
                    const name = document.getElementById('med-name').value.trim();
                    const dosage = document.getElementById('med-dosage').value.trim();
                    const frequency = document.getElementById('med-frequency').value.trim();
                    const duration = document.getElementById('med-duration').value.trim();
                    const quantity = document.getElementById('med-quantity').value.trim();

                    if (!name || !dosage || !frequency || !duration) {
                        toastr.error('Please fill in all required medication fields');
                        return;
                    }

                    // Store diagnosis separately for the prescription
                    window.currentDiagnosis = diagnosis;

                    medications.push({
                        name,
                        dosage,
                        frequency,
                        duration,
                        quantity
                    });
                    renderMedications();
                    document.getElementById('medication-modal').classList.add('hidden');
                });

                function renderMedications() {
                    const container = document.getElementById('medications-prescription-list');
                    if (medications.length === 0) {
                        container.innerHTML =
                            '<p class="text-sm text-gray-500 italic">No medications added yet. Click "Add Medication" to start.</p>';
                        return;
                    }
                    container.innerHTML = medications.map((med, index) =>
                        `<div class="p-3 sm:p-4 border border-gray-200 rounded-lg">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div>
                        <p class="font-medium text-gray-800 text-sm sm:text-base">${med.name} ${med.dosage}</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">${med.frequency}</p>
                        <p class="text-xs text-gray-500 mt-1">Duration: ${med.duration}${med.quantity ? ` • Quantity: ${med.quantity}` : ''}</p>
                    </div>
                    <button onclick="removeMedication(${index})" class="text-red-600 hover:text-red-700 text-xs sm:text-sm self-start sm:self-auto">Remove</button>
                </div>
            </div>`
                    ).join('');
                }

                window.removeMedication = function(index) {
                    medications.splice(index, 1);
                    renderMedications();
                };

                // Save Prescription
                document.getElementById('save-prescription-btn').addEventListener('click', function() {
                    const diagnosis = window.currentDiagnosis || '';
                    const messageEl = document.getElementById('prescription-message');

                    if (medications.length === 0) {
                        showMessage(messageEl, 'Please add at least one medication', 'error');
                        return;
                    }

                    fetch(`/doctor/appointments/${appointmentId}/prescription`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                diagnosis,
                                medications
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                showMessage(messageEl, result.msg, 'success');
                                medications = [];
                                window.currentDiagnosis = '';
                                renderMedications();
                                setTimeout(() => loadAppointmentDetails(), 2000);
                            } else {
                                showMessage(messageEl, result.msg || 'Failed to save prescription',
                                    'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(messageEl, 'An error occurred', 'error');
                        });
                });

                // Load available time slots when date changes
                document.getElementById('followup-date').addEventListener('change', function() {
                    const date = this.value;
                    const timeSelect = document.getElementById('followup-time');
                    const loadingMsg = document.getElementById('followup-time-loading');

                    if (!date) {
                        timeSelect.innerHTML = '<option value="">Select a date first</option>';
                        return;
                    }

                    // Show loading
                    loadingMsg.classList.remove('hidden');
                    timeSelect.innerHTML = '<option value="">Loading...</option>';
                    timeSelect.disabled = true;

                    fetch(`/doctor/appointments/available-slots?date=${date}`)
                        .then(response => response.json())
                        .then(result => {
                            loadingMsg.classList.add('hidden');
                            timeSelect.disabled = false;

                            if (result.status === 200 && result.data.success) {
                                const slots = result.data.slots;
                                if (slots.length > 0) {
                                    timeSelect.innerHTML = '<option value="">Select time</option>' +
                                        slots.map(slot => `<option value="${slot}">${slot}</option>`).join(
                                            '');
                                } else {
                                    timeSelect.innerHTML = '<option value="">No slots available</option>';
                                }
                            } else {
                                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            loadingMsg.classList.add('hidden');
                            timeSelect.disabled = false;
                            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                        });
                });

                // Schedule Follow-up
                document.getElementById('schedule-followup-btn').addEventListener('click', function() {
                    const date = document.getElementById('followup-date').value;
                    const time = document.getElementById('followup-time').value;
                    const reason = document.getElementById('followup-reason').value.trim();
                    const messageEl = document.getElementById('followup-message');

                    if (!date || !time) {
                        showMessage(messageEl, 'Please select date and time', 'error');
                        return;
                    }

                    fetch(`/doctor/appointments/${appointmentId}/follow-up`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                appointment_date: date,
                                appointment_time: time,
                                reason
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                showMessage(messageEl, result.msg, 'success');
                                document.getElementById('followup-date').value = '';
                                document.getElementById('followup-time').value = '';
                                document.getElementById('followup-reason').value = '';
                            } else {
                                showMessage(messageEl, result.msg || 'Failed to schedule follow-up',
                                    'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(messageEl, 'An error occurred', 'error');
                        });
                });

                // Save Vital Signs
                document.getElementById('save-vitals-btn').addEventListener('click', function() {
                    const bp = document.getElementById('vital-bp').value.trim();
                    const hr = document.getElementById('vital-hr').value.trim();
                    const temp = document.getElementById('vital-temp').value.trim();
                    const o2 = document.getElementById('vital-o2').value.trim();
                    const weight = document.getElementById('vital-weight').value.trim();
                    const height = document.getElementById('vital-height').value.trim();
                    const messageEl = document.getElementById('vitals-message');

                    if (!bp && !hr && !temp && !o2 && !weight && !height) {
                        showMessage(messageEl, 'Please enter at least one vital sign', 'error');
                        return;
                    }

                    // Send vitals to backend
                    fetch(`/doctor/appointments/${appointmentId}/vital-signs`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                blood_pressure: bp,
                                heart_rate: hr,
                                temperature: temp,
                                oxygen_saturation: o2,
                                weight: weight,
                                height: height
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                showMessage(messageEl, result.msg, 'success');
                                // Clear inputs
                                document.getElementById('vital-bp').value = '';
                                document.getElementById('vital-hr').value = '';
                                document.getElementById('vital-temp').value = '';
                                document.getElementById('vital-o2').value = '';
                                document.getElementById('vital-weight').value = '';
                                document.getElementById('vital-height').value = '';
                                // Reload appointment to show updated notes
                                setTimeout(() => loadAppointmentDetails(), 1500);
                            } else {
                                showMessage(messageEl, result.msg || 'Failed to save vital signs', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(messageEl, 'An error occurred', 'error');
                        });
                });

                // Complete Appointment - Show Modal
                document.getElementById('complete-appointment-btn').addEventListener('click', function() {
                    document.getElementById('complete-appointment-modal').classList.remove('hidden');
                });

                // Cancel Complete Modal
                document.getElementById('cancel-complete-btn').addEventListener('click', function() {
                    document.getElementById('complete-appointment-modal').classList.add('hidden');
                });

                // Confirm Complete Appointment
                document.getElementById('confirm-complete-btn').addEventListener('click', function() {
                    const modal = document.getElementById('complete-appointment-modal');
                    const btn = this;

                    btn.disabled = true;
                    btn.textContent = 'Processing...';

                    fetch(`/doctor/appointments/${appointmentId}/complete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                modal.classList.add('hidden');
                                toastr.success(result.msg);
                                setTimeout(() => {
                                    window.location.href = '{{ route('doctor.appointments') }}';
                                }, 1000);
                            } else {
                                toastr.error(result.msg || 'Failed to complete appointment');
                                btn.disabled = false;
                                btn.textContent = 'Confirm';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred');
                            btn.disabled = false;
                            btn.textContent = 'Confirm';
                        });
                });

                // Update Status Options based on current status
                function updateStatusOptions(currentStatus) {
                    const select = document.getElementById('status-select');
                    select.innerHTML = '<option value="">Select status...</option>';

                    // Define allowed transitions
                    const allowedTransitions = {
                        'pending': ['confirmed'],
                        'confirmed': ['checked_in', 'in_progress'],
                        'checked_in': ['in_progress'],
                        'in_progress': []
                    };

                    const statusLabels = {
                        'confirmed': 'Confirmed',
                        'checked_in': 'Checked In',
                        'in_progress': 'In Progress'
                    };

                    const allowedStatuses = allowedTransitions[currentStatus] || [];
                    allowedStatuses.forEach(status => {
                        const option = document.createElement('option');
                        option.value = status;
                        option.textContent = statusLabels[status];
                        select.appendChild(option);
                    });
                }

                // Update Status Handler
                document.getElementById('update-status-btn').addEventListener('click', function() {
                    const newStatus = document.getElementById('status-select').value;
                    const messageEl = document.getElementById('status-message');

                    if (!newStatus) {
                        showMessage(messageEl, 'Please select a status', 'error');
                        return;
                    }

                    // Validate status transition
                    const currentStatus = appointmentData.appointment.status;
                    const allowedTransitions = {
                        'pending': ['confirmed'],
                        'confirmed': ['checked_in', 'in_progress'],
                        'checked_in': ['in_progress'],
                        'in_progress': []
                    };

                    if (!allowedTransitions[currentStatus] || !allowedTransitions[currentStatus].includes(
                            newStatus)) {
                        showMessage(messageEl, 'Invalid status transition', 'error');
                        return;
                    }

                    this.disabled = true;
                    this.textContent = 'Updating...';

                    fetch(`/doctor/appointments/${appointmentId}/update-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 200) {
                                showMessage(messageEl, 'Status updated successfully!', 'success');
                                // Reload appointment details
                                setTimeout(() => {
                                    loadAppointmentDetails();
                                }, 1000);
                            } else {
                                showMessage(messageEl, result.msg || 'Failed to update status', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(messageEl, 'An error occurred', 'error');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.textContent = 'Update';
                        });
                });

                function showMessage(element, message, type) {
                    element.textContent = message;
                    element.className = `mt-2 text-sm ${type === 'success' ? 'text-green-600' : 'text-red-600'}`;
                    element.classList.remove('hidden');
                    setTimeout(() => element.classList.add('hidden'), 5000);
                }
            });
        </script>
    @endpush
@endsection
