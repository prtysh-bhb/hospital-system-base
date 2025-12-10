<!-- step-2.blade.php -->
<!-- Header -->
<header class="bg-white shadow-sm">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <h1 class="text-xl sm:text-2xl font-bold text-sky-700"><a href="{{ route('home') }}">MediCare Hospital</a></h1>
        <p class="text-xs sm:text-sm text-gray-600">Book Your Appointment</p>
    </div>
</header>

<!-- Progress Steps -->
<form action="{{ route('booking.store') }}" method="POST" id="step2Form">
    @csrf
    <input type="hidden" name="step" value="2">
    <input type="hidden" name="date" id="selectedDateInput" value="{{ $selectedDate }}">
    <input type="hidden" name="slot" id="selectedSlotInput" value="">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <!-- Progress steps -->
            <div class="flex flex-col items-center flex-1">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                    ✓</div>
                <span class="text-xs sm:text-xs text-sky-600 font-medium">Doctor</span>
            </div>
            <div class="flex-1 h-1 bg-sky-600 mx-1 sm:mx-2"></div>
            <div class="flex flex-col items-center flex-1">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                    2</div>
                <span class="text-xs sm:text-xs text-sky-600 font-medium text-center">Date & Time</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
            <div class="flex flex-col items-center flex-1">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                    3</div>
                <span class="text-xs sm:text-xs text-gray-500 text-center">Details</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
            <div class="flex flex-col items-center flex-1">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                    4</div>
                <span class="text-xs sm:text-xs text-gray-500 text-center">Confirm</span>
            </div>
        </div>

        {{-- Selected Doctor Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3 sm:mb-4">Selected Doctor</h3>
            <div class="flex items-center space-x-3 sm:space-x-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->first_name . ' ' . $doctor->last_name) }}&background={{ $doctor->doctorProfile->specialty->color ?? '0ea5e9' }}&color=fff"
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base">
                        {{ $doctor->first_name }} {{ $doctor->last_name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-sky-600">
                        {{ $doctor->doctorProfile->specialty->name ?? 'NA' }}
                    </p>
                    <p class="text-xs text-gray-600">
                        Consultation Fee: ₹{{ $doctor->doctorProfile->consultation_fee ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        {{-- SELECT DATE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6">Select Date</h2>

            {{-- MONTH NAVIGATION --}}
            <div class="flex items-center justify-between mb-4">
                <button type="button" id="prevMonthBtn"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
                <h3 id="currentMonthLabel" class="text-lg font-semibold text-gray-800"></h3>
                <button type="button" id="nextMonthBtn"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- CALENDAR HEADER --}}
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2">
                <div class="text-center text-xs font-medium text-gray-500 py-2">Sun</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Mon</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Tue</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Wed</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Thu</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Fri</div>
                <div class="text-center text-xs font-medium text-gray-500 py-2">Sat</div>
            </div>

            {{-- CALENDAR DAYS (Dynamic) --}}
            <div id="calendarDays" class="grid grid-cols-7 gap-1 sm:gap-2 mb-6">
                <!-- Calendar days will be rendered by JavaScript -->
            </div>

            {{-- TIME SLOTS --}}
            <label class="block text-sm font-medium text-gray-700 mb-3">Available Time Slots</label>

            <div id="slotsContainer" class="grid grid-cols-3 md:grid-cols-4 gap-2">
                <div class="col-span-full text-center py-4">
                    <p class="text-gray-500 text-sm">Select a date to view available slots</p>
                </div>
            </div>

            <div id="slotsLoading" class="hidden col-span-full text-center py-4">
                <svg class="animate-spin h-6 w-6 text-sky-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-500 text-sm mt-2">Loading available slots...</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mb-6">
            <a href="{{ route('booking', ['step' => 1]) }}"
                class="px-6 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-lg text-sm sm:text-base font-medium hover:bg-gray-300">Back</a>
            <button type="submit" id="nextStepBtn" disabled
                class="px-6 py-3 bg-sky-600 text-white rounded-lg text-sm font-medium hover:bg-sky-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                Next Step
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('step2Form');
        const nextBtn = document.getElementById('nextStepBtn');
        const dateInput = document.getElementById('selectedDateInput');
        const slotInput = document.getElementById('selectedSlotInput');
        const calendarDays = document.getElementById('calendarDays');
        const slotsContainer = document.getElementById('slotsContainer');
        const slotsLoading = document.getElementById('slotsLoading');
        const prevMonthBtn = document.getElementById('prevMonthBtn');
        const nextMonthBtn = document.getElementById('nextMonthBtn');
        const currentMonthLabel = document.getElementById('currentMonthLabel');

        // Date configuration
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const minDate = new Date(today);
        const maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() + 60); // 60 days from today

        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();
        let selectedDate = dateInput.value || null;

        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Initialize calendar
        renderCalendar();
        updateNavigationButtons();

        // If there's a pre-selected date, load its slots
        // Otherwise, auto-select today and fetch its slots
        if (selectedDate) {
            fetchSlots(selectedDate);
        } else {
            // Auto-select today
            selectedDate = formatDate(today);
            dateInput.value = selectedDate;
            renderCalendar();
            fetchSlots(selectedDate);
        }

        // Month navigation
        prevMonthBtn.addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
            updateNavigationButtons();
        });

        nextMonthBtn.addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
            updateNavigationButtons();
        });

        function updateNavigationButtons() {
            // Disable prev button if showing current month
            const currentMonthStart = new Date(currentYear, currentMonth, 1);
            const todayMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            prevMonthBtn.disabled = currentMonthStart <= todayMonth;

            // Disable next button if showing max month
            const maxMonth = new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);
            const displayMonth = new Date(currentYear, currentMonth, 1);
            nextMonthBtn.disabled = displayMonth >= maxMonth;
        }

        function renderCalendar() {
            // Update month label
            currentMonthLabel.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            // Get first day of month and total days
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const startDayOfWeek = firstDay.getDay();
            const totalDays = lastDay.getDate();

            let html = '';

            // Add empty cells for days before the first day of month
            for (let i = 0; i < startDayOfWeek; i++) {
                html += '<div class="p-2 sm:p-3"></div>';
            }

            // Add days
            for (let day = 1; day <= totalDays; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const dateStr = formatDate(date);

                const isPast = date < today;
                const isFuture = date > maxDate;
                const isDisabled = isPast || isFuture;
                const isSelected = dateStr === selectedDate;
                const isToday = date.getTime() === today.getTime();

                let classes = 'p-2 sm:p-3 rounded-lg text-xs sm:text-sm text-center transition-colors';

                if (isDisabled) {
                    classes += ' text-gray-300 bg-gray-50 cursor-not-allowed';
                } else if (isSelected) {
                    classes += ' bg-sky-600 text-white font-semibold shadow-md cursor-pointer';
                } else if (isToday) {
                    classes +=
                        ' bg-sky-100 text-sky-800 font-semibold cursor-pointer border-2 border-sky-600 hover:bg-sky-200';
                } else {
                    classes += ' text-gray-800 hover:bg-sky-50 cursor-pointer border border-gray-200';
                }

                if (!isDisabled) {
                    html += `<div class="calendar-day ${classes}" data-date="${dateStr}">${day}</div>`;
                } else {
                    html += `<div class="${classes}">${day}</div>`;
                }
            }

            calendarDays.innerHTML = html;

            // Re-attach event listeners for date selection
            document.querySelectorAll('.calendar-day').forEach(dayEl => {
                dayEl.addEventListener('click', function() {
                    const date = this.dataset.date;
                    if (!date) return;

                    // Update selection
                    selectedDate = date;
                    dateInput.value = date;

                    // Clear slot selection
                    slotInput.value = '';
                    nextBtn.disabled = true;

                    // Re-render calendar to update selection
                    renderCalendar();

                    // Fetch slots via AJAX
                    fetchSlots(date);
                });
            });
        }

        function fetchSlots(date) {
            // Show loading
            slotsContainer.classList.add('hidden');
            slotsLoading.classList.remove('hidden');

            fetch(`{{ route('get.time.slots') }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    slotsLoading.classList.add('hidden');
                    slotsContainer.classList.remove('hidden');

                    if (data.slots && data.slots.length > 0) {
                        let html = '';
                        data.slots.forEach(slot => {
                            html += `
                                <label class="cursor-pointer slot-option">
                                    <input type="radio" name="slot" value="${slot}" class="hidden peer slot-radio">
                                    <div class="px-3 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-xs text-center
                                               hover:border-sky-600 hover:text-sky-600
                                               peer-checked:bg-sky-600 peer-checked:text-white peer-checked:border-sky-600 transition-colors">
                                        ${slot}
                                    </div>
                                </label>
                            `;
                        });
                        slotsContainer.innerHTML = html;

                        // Attach slot selection handlers
                        document.querySelectorAll('.slot-radio').forEach(radio => {
                            radio.addEventListener('change', function() {
                                slotInput.value = this.value;
                                nextBtn.disabled = false;

                                // Highlight selected slot
                                document.querySelectorAll('.slot-option').forEach(opt => {
                                    opt.classList.remove('ring-2', 'ring-sky-500');
                                });
                                this.closest('.slot-option').classList.add('ring-2',
                                    'ring-sky-500');
                            });
                        });
                    } else {
                        slotsContainer.innerHTML = `
                            <p class="text-red-500 text-sm col-span-full text-center py-4">
                                No slots available for selected date. Please choose another date.
                            </p>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching slots:', error);
                    slotsLoading.classList.add('hidden');
                    slotsContainer.classList.remove('hidden');
                    slotsContainer.innerHTML = `
                        <p class="text-red-500 text-sm col-span-full text-center py-4">
                            Failed to load slots. Please try again.
                        </p>
                    `;
                });
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    });
</script>
