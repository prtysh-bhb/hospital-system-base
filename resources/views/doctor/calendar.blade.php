@extends('layouts.doctor')

@section('title', 'My Schedule')

@section('page-title', 'My Schedule & Availability')

@section('content')
    <!-- Calendar Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="p-4 sm:p-6 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <button id="prevPeriod" class="px-2 sm:px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 id="currentPeriod" class="text-lg sm:text-xl font-semibold text-gray-800">
                    {{ date('F Y') }}
                </h3>
                <button id="nextPeriod" class="px-2 sm:px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button id="viewMonth"
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg text-xs sm:text-sm">
                    Month
                </button>
                <button id="viewWeek"
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-xs sm:text-sm">
                    Week
                </button>
                <button id="viewDay"
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-xs sm:text-sm">
                    Day
                </button>
            </div>
        </div>

        <!-- Month View -->
        <div id="monthView" class="p-3 sm:p-6">
            <!-- Days Header -->
            <div class="grid grid-cols-7 gap-2 sm:gap-4 mb-3 sm:mb-4">
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Sun</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Mon</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Tue</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Wed</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Thu</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Fri</div>
                <div class="text-center text-xs sm:text-sm font-semibold text-gray-600">Sat</div>
            </div>

            <!-- Calendar Days Container -->
            <div id="calendarDays" class="grid grid-cols-7 gap-1 sm:gap-2 md:gap-4">
                <div class="col-span-7 text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                    <p class="mt-2 text-gray-500">Loading calendar...</p>
                </div>
            </div>
        </div>

        <!-- Week View -->
        <div id="weekView" class="hidden p-4 sm:p-6"></div>

        <!-- Day View -->
        <div id="dayView" class="hidden p-4 sm:p-6"></div>
    </div>

    <!-- Weekly Availability Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Weekly Availability Schedule</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Set your regular working hours for each day</p>
        </div>
        <div class="p-4 sm:p-6">
            <div id="weeklySchedule">
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                    <p class="mt-2 text-gray-500">Loading schedule...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Appointment Details</h3>
                    <button onclick="closeAppointmentModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="appointmentModalContent" class="p-6">
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Appointments Modal -->
    <div id="dateAppointmentsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800" id="dateModalTitle">Appointments</h3>
                    <button onclick="closeDateModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="dateModalContent" class="p-6">
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Info (Remove in production) -->
    <div id="debugInfo" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
        <h4 class="font-semibold text-yellow-800">Debug Information</h4>
        <pre id="debugContent" class="text-xs mt-2"></pre>
    </div>
@endsection

@push('scripts')
    <script>
        let currentMonth = '{{ date('Y-m') }}';
        let currentView = 'month'; // month, week, day
        let currentDate = new Date();

        $(document).ready(function() {
            console.log('Document ready, loading calendar for:', currentMonth);
            loadCalendar(currentMonth);
            loadWeeklySchedule();

            // View switchers
            $('#viewMonth').on('click', function() {
                switchView('month');
            });

            $('#viewWeek').on('click', function() {
                switchView('week');
            });

            $('#viewDay').on('click', function() {
                switchView('day');
            });

            $('#prevPeriod').on('click', function() {
                if (currentView === 'month') {
                    const [year, month] = currentMonth.split('-');
                    const date = new Date(year, month - 1, 1);
                    date.setMonth(date.getMonth() - 1);
                    currentMonth = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                    console.log('Loading previous month:', currentMonth);
                    loadCalendar(currentMonth);
                } else if (currentView === 'week') {
                    currentDate.setDate(currentDate.getDate() - 7);
                    loadWeekView();
                } else if (currentView === 'day') {
                    currentDate.setDate(currentDate.getDate() - 1);
                    loadDayView();
                }
            });

            $('#nextPeriod').on('click', function() {
                if (currentView === 'month') {
                    const [year, month] = currentMonth.split('-');
                    const date = new Date(year, month - 1, 1);
                    date.setMonth(date.getMonth() + 1);
                    currentMonth = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                    console.log('Loading next month:', currentMonth);
                    loadCalendar(currentMonth);
                } else if (currentView === 'week') {
                    currentDate.setDate(currentDate.getDate() + 7);
                    loadWeekView();
                } else if (currentView === 'day') {
                    currentDate.setDate(currentDate.getDate() + 1);
                    loadDayView();
                }
            });
        });

        function switchView(view) {
            currentView = view;

            // Update button styles
            $('#viewMonth, #viewWeek, #viewDay').removeClass('bg-sky-600 text-white').addClass(
                'border border-gray-300 text-gray-700');
            if (view === 'month') {
                $('#viewMonth').removeClass('border border-gray-300 text-gray-700').addClass('bg-sky-600 text-white');
                $('#monthView').removeClass('hidden');
                $('#weekView').addClass('hidden');
                $('#dayView').addClass('hidden');
                loadCalendar(currentMonth);
            } else if (view === 'week') {
                $('#viewWeek').removeClass('border border-gray-300 text-gray-700').addClass('bg-sky-600 text-white');
                $('#monthView').addClass('hidden');
                $('#weekView').removeClass('hidden');
                $('#dayView').addClass('hidden');
                loadWeekView();
            } else if (view === 'day') {
                $('#viewDay').removeClass('border border-gray-300 text-gray-700').addClass('bg-sky-600 text-white');
                $('#monthView').addClass('hidden');
                $('#weekView').addClass('hidden');
                $('#dayView').removeClass('hidden');
                loadDayView();
            }
        }

        function loadWeekView() {
            const startOfWeek = new Date(currentDate);
            let day = currentDate.getDay();
            if (day === 0) day = 7; // Convert Sunday to day 7
            startOfWeek.setDate(currentDate.getDate() - day + 1);

            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);

            $('#currentPeriod').text(
                `${startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`
            );

            showLoading('#weekView', 'Loading week view...');

            const weekDates = [];
            for (let i = 0; i < 7; i++) {
                const day = new Date(startOfWeek);
                day.setDate(startOfWeek.getDate() + i);
                weekDates.push(day.toISOString().split('T')[0]);
            }

            Promise.all(
                weekDates.map(date =>
                    fetch(`{{ route('doctor.calendar.appointments') }}?date=${date}`)
                    .then(response => response.json())
                    .catch(() => ({
                        success: false,
                        appointments: []
                    }))
                )
            ).then(responses => {
                renderWeekView(weekDates, responses);

                // 🔹 Highlight Current Day After Rendering
                setTimeout(() => {
                    const today = new Date().toISOString().split('T')[0];
                    const todayCell = document.querySelector(`[data-date="${today}"]`);
                    if (todayCell) {
                        todayCell.classList.add('active-day');
                    }
                }, 50);
            });
        }


        function renderWeekView(weekDates, responses) {
            const statusColors = {
                'completed': 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'confirmed': 'bg-sky-100 text-sky-700 border-sky-200',
                'pending': 'bg-amber-100 text-amber-700 border-amber-200',
                'cancelled': 'bg-red-100 text-red-700 border-red-200'
            };

            let html = `
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <!-- Week Header -->
                    <div class="grid grid-cols-8 border-b border-gray-200 bg-gray-50">
                        <div class="p-4 border-r border-gray-200">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Time</p>
                        </div>
            `;

            // Add day headers
            weekDates.forEach(function(dateStr, index) {
                const date = new Date(dateStr);
                const dayName = date.toLocaleDateString('en-US', {
                    weekday: 'short'
                });
                const dayNum = date.getDate();
                const monthName = date.toLocaleDateString('en-US', {
                    month: 'short'
                });
                const isToday = dateStr === new Date().toISOString().split('T')[0];

                html += `
                    <div class="p-4 text-center border-r border-gray-200 ${isToday ? 'bg-sky-50' : ''}">
                        <p class="text-xs font-semibold text-gray-500 uppercase">${dayName}</p>
                        <p class="text-lg font-bold ${isToday ? 'text-sky-600' : 'text-gray-800'}">${dayNum}</p>
                        <p class="text-xs text-gray-500">${monthName}</p>
                    </div>
                `;
            });

            html += `</div>`;

            // Time slots (7 AM to 7 PM)
            const timeSlots = [
                '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM'
            ];

            timeSlots.forEach(timeSlot => {
                html += `<div class="grid grid-cols-8 border-b border-gray-100">`;

                // Time column
                html += `
                    <div class="p-4 border-r border-gray-200 bg-gray-50">
                        <p class="text-sm font-medium text-gray-600">${timeSlot}</p>
                    </div>
                `;

                // Day columns
                weekDates.forEach(function(dateStr, index) {
                    const date = new Date(dateStr);
                    const isToday = dateStr === new Date().toISOString().split('T')[0];
                    const appointments = responses[index]?.success ? responses[index].appointments : [];

                    // Filter appointments for this time slot
                    const slotAppointments = appointments.filter(apt => {
                        return apt.time === timeSlot;
                    });

                    html += `
                        <div class="p-2 border-r border-gray-100 min-h-20 ${isToday ? 'bg-sky-50' : ''}">
                    `;

                    if (slotAppointments.length > 0) {
                        slotAppointments.forEach(apt => {
                            const statusClass = statusColors[apt.status] ||
                                'bg-gray-100 text-gray-700';
                            html += `
                                <div class="text-xs p-2 mb-1 ${statusClass} rounded cursor-pointer hover:shadow-sm border" 
                                    onclick="showAppointmentDetailsById(${apt.id})" 
                                    title="${apt.time} - ${apt.patient_name}">
                                    <div class="font-semibold truncate">${apt.patient_name.split(' ')[0]}</div>
                                    <div class="text-gray-600 truncate">${apt.doctor_name || apt.type}</div>
                                </div>
                            `;
                        });
                    } else {
                        html += `<div class="h-full min-h-16"></div>`;
                    }

                    html += `</div>`;
                });

                html += `</div>`;
            });

            html += `</div>`;
            $('#weekView').html(html);
        }

        function loadDayView() {
            const dateStr = currentDate.toISOString().split('T')[0];
            $('#currentPeriod').text(currentDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }));

            showLoading('#dayView', 'Loading day view...');

            $.ajax({
                url: '{{ route('doctor.calendar.appointments') }}',
                method: 'GET',
                data: {
                    date: dateStr
                },
                success: function(response) {
                    if (response.success) {
                        renderDayView(response.appointments, response.date);
                    } else {
                        showError('#dayView', 'Failed to load appointments');
                    }
                },
                error: function() {
                    showError('#dayView', 'Failed to load day view');
                }
            });
        }

        function renderDayView(appointments, dateTitle) {
            const statusColors = {
                'completed': 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'confirmed': 'bg-sky-100 text-sky-700 border-sky-200',
                'pending': 'bg-amber-100 text-amber-700 border-amber-200',
                'cancelled': 'bg-red-100 text-red-700 border-red-200'
            };

            let html = `
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <!-- Day Header -->
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-xl font-semibold text-gray-800">${dateTitle}</h3>
                    </div>

                    <!-- Time Grid -->
                    <div class="divide-y divide-gray-100">
            `;

            // Time slots for the day (7 AM to 7 PM)
            const timeSlots = [
                '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM'
            ];

            timeSlots.forEach(timeSlot => {
                const slotAppointments = appointments.filter(apt => {
                    return apt.time === timeSlot;
                });

                html += `
                    <div class="grid grid-cols-12 p-4 hover:bg-gray-50 transition-colors">
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">${timeSlot}</p>
                        </div>
                        <div class="col-span-10">
                `;

                if (slotAppointments.length > 0) {
                    slotAppointments.forEach(apt => {
                        const statusClass = statusColors[apt.status] || 'bg-gray-100 text-gray-700';
                        html += `
                            <div class="mb-3 p-4 ${statusClass} rounded-lg border cursor-pointer hover:shadow-sm transition-shadow" 
                                onclick="showAppointmentDetailsById(${apt.id})">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-semibold text-gray-800">${apt.patient_name}</span>
                                            <span class="text-xs px-2 py-1 rounded-full ${statusClass} border">${apt.status.toUpperCase()}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">${apt.doctor_name || apt.type}</span> • ${apt.duration} min
                                        </div>
                                        ${apt.reason ? `<div class="text-sm text-gray-500 mt-2">${apt.reason}</div>` : ''}
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-800">${apt.time}</div>
                                        <div class="text-xs text-gray-500">${apt.appointment_number}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<p class="text-sm text-gray-400 italic">No appointments scheduled</p>`;
                }

                html += `</div></div>`;
            });

            html += `</div></div>`;
            $('#dayView').html(html);
        }

        function loadCalendar(month) {
            console.log('Loading calendar for month:', month);

            showLoading('#calendarDays', 'Loading calendar...');

            $.ajax({
                url: '{{ route('doctor.calendar.data') }}',
                method: 'GET',
                data: {
                    month: month
                },
                success: function(response) {
                    console.log('Calendar response:', response);

                    if (response.success) {
                        $('#currentPeriod').text(response.data.month_name);
                        renderCalendar(response.data.days);
                    } else {
                        showError('#calendarDays', response.message || 'Failed to load calendar');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Calendar AJAX error:', error);
                    console.log('XHR response:', xhr.responseText);
                    showError('#calendarDays', 'Failed to load calendar. Please check console for details.');
                }
            });
        }

        function renderCalendar(days) {
            console.log('Rendering calendar days:', days);

            if (!days || !Array.isArray(days)) {
                showError('#calendarDays', 'Invalid calendar data received');
                return;
            }

            let html = '';

            days.forEach(function(day) {
                const isCurrentMonth = day.is_current_month;
                const isToday = day.is_today;
                const hasAppointments = day.appointments && day.appointments.length > 0;

                let classes = 'aspect-square border rounded-lg p-1 sm:p-2 min-h-[60px] sm:min-h-[80px]';

                if (isCurrentMonth) {
                    classes += ' cursor-pointer';
                }

                if (!isCurrentMonth) {
                    classes += ' bg-gray-50 border-gray-200';
                } else if (isToday) {
                    classes += ' border-2 border-sky-600 bg-sky-50';
                } else {
                    classes += ' border-gray-300 hover:border-gray-400';
                }

                html += `<div class="${classes}" data-date="${day.date}">`;
                html +=
                    `<div class="text-xs sm:text-sm font-medium ${isCurrentMonth ? (isToday ? 'text-sky-700 font-bold' : 'text-gray-800') : 'text-gray-400'}">${day.day}</div>`;

                if (hasAppointments && isCurrentMonth) {
                    html += '<div class="mt-1 space-y-0.5 sm:space-y-1">';
                    day.appointments.slice(0, 2).forEach(function(apt) {
                        let badgeClass = 'bg-gray-100 text-gray-700';
                        if (apt.status === 'completed') {
                            badgeClass = 'bg-emerald-100 text-emerald-700';
                        } else if (apt.status === 'confirmed' || apt.status === 'checked_in' || apt
                            .status === 'in_progress') {
                            badgeClass = 'bg-sky-100 text-sky-700';
                        } else if (apt.status === 'pending') {
                            badgeClass = 'bg-amber-100 text-amber-700';
                        }
                        html +=
                            `<div class="text-xs ${badgeClass} px-1 py-0.5 rounded truncate hidden sm:block" onclick="event.stopPropagation(); showAppointmentDetailsById(${apt.id})">${apt.time}</div>`;
                    });
                    if (day.appointments.length > 2) {
                        html +=
                            `<div class="text-xs text-gray-500 px-1 hidden sm:block" onclick="event.stopPropagation(); showDateAppointments('${day.date}')">+${day.appointments.length - 2} more</div>`;
                    }
                    html += '</div>';
                }

                html += '</div>';
            });

            $('#calendarDays').html(html);

            // Add click handlers
            $('#calendarDays > div').on('click', function() {
                const date = $(this).data('date');
                const isCurrentMonth = !$(this).hasClass('bg-gray-50');

                if (date && isCurrentMonth) {
                    showDateAppointments(date);
                }
            });
        }

        function loadWeeklySchedule() {
            console.log('Loading weekly schedule...');

            $.ajax({
                url: '{{ route('doctor.calendar.schedule') }}',
                method: 'GET',
                success: function(response) {
                    console.log('Schedule response:', response);

                    if (response.success) {
                        renderWeeklySchedule(response.schedule);
                    } else {
                        showError('#weeklySchedule', response.message || 'Failed to load schedule');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Schedule AJAX error:', error);
                    showError('#weeklySchedule', 'Failed to load schedule');
                }
            });
        }

        function renderWeeklySchedule(schedule) {
            console.log('Rendering schedule:', schedule);

            if (!schedule || !Array.isArray(schedule)) {
                showError('#weeklySchedule', 'Invalid schedule data');
                return;
            }

            let html = '<div class="space-y-3 sm:space-y-4">';

            schedule.forEach(function(day) {
                const isAvailable = day.is_available;
                const bgClass = isAvailable ? '' : 'bg-gray-50';
                const dayNum = day.day_of_week;

                html +=
                    `<div class="schedule-day flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 border border-gray-200 rounded-lg ${bgClass}" data-day="${dayNum}">`;
                html += `<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 flex-1">`;
                html += `<div class="flex items-center gap-3 sm:gap-4">`;
                html +=
                    `<input type="checkbox" class="day-checkbox w-4 h-4 sm:w-5 sm:h-5 text-sky-600 rounded" ${isAvailable ? 'checked' : ''}>`;
                html +=
                    `<span class="font-medium ${isAvailable ? 'text-gray-800' : 'text-gray-500'} text-sm sm:text-base sm:w-24">${day.day_name}</span>`;
                html += `</div>`;

                if (isAvailable) {
                    // Convert time format (e.g., "9:00 AM" to "09:00")
                    const startTime = convertTo24Hour(day.start_time);
                    const endTime = convertTo24Hour(day.end_time);

                    html += `<div class="flex items-center gap-2 flex-1">`;
                    html +=
                        `<input type="time" class="start-time px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-sm sm:text-base flex-1 sm:flex-none" value="${startTime}">`;
                    html += `<span class="text-gray-500 text-xs sm:text-sm">to</span>`;
                    html +=
                        `<input type="time" class="end-time px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-sm sm:text-base flex-1 sm:flex-none" value="${endTime}">`;
                    html += `</div>`;
                } else {
                    html += `<span class="text-gray-400 text-sm sm:text-base unavailable-text">Unavailable</span>`;
                }

                html += `</div>`;
                html += `</div>`;
            });

            html += '</div>';

            html += `
            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-end gap-3">
                <button id="cancelSchedule" class="px-4 sm:px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm sm:text-base">
                    Cancel
                </button>
                <button id="saveSchedule" class="px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Save Changes
                </button>
            </div>
        `;

            $('#weeklySchedule').html(html);

            // Attach event handlers
            attachScheduleHandlers();
        }

        function convertTo24Hour(time12h) {
            if (!time12h) return '09:00';

            const [time, modifier] = time12h.split(' ');
            let [hours, minutes] = time.split(':');

            hours = parseInt(hours, 10);

            if (modifier === 'PM' && hours !== 12) {
                hours = hours + 12;
            } else if (modifier === 'AM' && hours === 12) {
                hours = 0;
            }

            return `${String(hours).padStart(2, '0')}:${minutes}`;
        }

        function attachScheduleHandlers() {
            // Handle checkbox changes
            $('.day-checkbox').on('change', function() {
                const $dayRow = $(this).closest('.schedule-day');
                const isChecked = $(this).is(':checked');
                const $container = $dayRow.find('.flex-col').first();

                if (isChecked) {
                    // Enable the day
                    $dayRow.removeClass('bg-gray-50');
                    $container.find('span').first().removeClass('text-gray-500').addClass('text-gray-800');

                    // Remove unavailable text
                    $container.find('.unavailable-text').remove();

                    // Add time inputs
                    const timeInputsHtml = `
                        <div class="flex items-center gap-2 flex-1">
                            <input type="time" class="start-time px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-sm sm:text-base flex-1 sm:flex-none" value="09:00">
                            <span class="text-gray-500 text-xs sm:text-sm">to</span>
                            <input type="time" class="end-time px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-sm sm:text-base flex-1 sm:flex-none" value="17:00">
                        </div>
                    `;
                    $container.append(timeInputsHtml);
                } else {
                    // Disable the day
                    $dayRow.addClass('bg-gray-50');
                    $container.find('span').first().removeClass('text-gray-800').addClass('text-gray-500');

                    // Remove time inputs div
                    $container.find('.flex.items-center.gap-2').remove();

                    // Add unavailable text
                    $container.append(
                        '<span class="text-gray-400 text-sm sm:text-base unavailable-text">Unavailable</span>');
                }
            });

            // Save schedule
            $('#saveSchedule').on('click', function() {
                saveSchedule();
            });

            // Cancel changes
            $('#cancelSchedule').on('click', function() {
                loadWeeklySchedule();
            });
        }

        function saveSchedule() {
            const schedules = [];
            let hasError = false;

            $('.schedule-day').each(function() {
                const dayNum = parseInt($(this).data('day'));
                const isAvailable = $(this).find('.day-checkbox').is(':checked');
                const startTime = $(this).find('.start-time').val();
                const endTime = $(this).find('.end-time').val();

                if (isAvailable && (!startTime || !endTime)) {
                    alert('Please set both start and end times for all available days');
                    hasError = true;
                    return false;
                }

                if (isAvailable && startTime >= endTime) {
                    alert('End time must be after start time for all days');
                    hasError = true;
                    return false;
                }

                schedules.push({
                    day_of_week: dayNum,
                    is_available: isAvailable ? true : false,
                    start_time: startTime || null,
                    end_time: endTime || null,
                    slot_duration: 30
                });
            });

            if (hasError) return;

            console.log('Sending schedules:', schedules);

            // Show loading
            $('#saveSchedule').prop('disabled', true).html(
                '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Saving...'
            );

            $.ajax({
                url: '{{ route('doctor.calendar.schedule.update') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    schedules: schedules
                }),
                success: function(response) {
                    console.log('Save response:', response);
                    if (response.success) {
                        // Show success message
                        const successMsg = $(
                            '<div class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">Schedule updated successfully!</div>'
                        );
                        $('body').append(successMsg);
                        setTimeout(function() {
                            successMsg.fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);

                        // Reload schedule
                        renderWeeklySchedule(response.schedule);
                    } else {
                        alert('Failed to update schedule: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.error('Schedule update error:', xhr);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'Failed to update schedule. Please try again.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    alert(errorMsg);
                },
                complete: function() {
                    $('#saveSchedule').prop('disabled', false).html('Save Changes');
                }
            });
        }

        function showDateAppointments(date) {
            const modal = document.getElementById('dateAppointmentsModal');
            const modalContent = document.getElementById('dateModalContent');
            const modalTitle = document.getElementById('dateModalTitle');

            modal.classList.remove('hidden');

            modalContent.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                </div>
            `;

            fetch(`{{ route('doctor.calendar.appointments') }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalTitle.textContent = `Appointments for ${data.date}`;

                        if (data.appointments.length === 0) {
                            modalContent.innerHTML = `
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-600">No appointments found for this date.</p>
                                </div>
                            `;
                        } else {
                            const statusColors = {
                                'confirmed': 'bg-green-100 text-green-800 border-green-200',
                                'pending': 'bg-amber-100 text-amber-800 border-amber-200',
                                'completed': 'bg-sky-100 text-sky-800 border-sky-200',
                                'cancelled': 'bg-red-100 text-red-800 border-red-200'
                            };

                            const appointmentsHtml = data.appointments.map(apt => `
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" onclick="closeDateModal(); showAppointmentDetailsById(${apt.id})">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">${apt.time}</p>
                                            <p class="text-xs text-gray-500">#${apt.appointment_number}</p>
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColors[apt.status] || 'bg-gray-100 text-gray-800 border-gray-200'} border">
                                            ${apt.status.toUpperCase()}
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-gray-700 font-medium">${apt.patient_name}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-gray-700">${apt.type}</span>
                                        </div>
                                        ${apt.reason ? `
                                                                                                        <div class="flex items-start text-sm">
                                                                                                            <svg class="w-4 h-4 mr-2 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                                                            </svg>
                                                                                                            <span class="text-gray-600">${apt.reason}</span>
                                                                                                        </div>
                                                                                                        ` : ''}
                                    </div>
                                </div>
                            `).join('');

                            modalContent.innerHTML = `
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    ${appointmentsHtml}
                                </div>
                            `;
                        }
                    } else {
                        modalContent.innerHTML = `
                            <div class="text-center py-8">
                                <p class="text-red-600">Failed to load appointments.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-600">An error occurred while loading appointments.</p>
                        </div>
                    `;
                });
        }

        function closeDateModal() {
            document.getElementById('dateAppointmentsModal').classList.add('hidden');
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'completed':
                    return 'bg-emerald-100 text-emerald-700';
                case 'confirmed':
                    return 'bg-sky-100 text-sky-700';
                case 'pending':
                    return 'bg-amber-100 text-amber-700';
                default:
                    return 'bg-gray-100 text-gray-700';
            }
        }

        function closeModal() {
            $('.fixed.inset-0').remove();
        }

        function showLoading(selector, message) {
            $(selector).html(`
            <div class="col-span-7 text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                <p class="mt-2 text-gray-500">${message}</p>
            </div>
        `);
        }

        function showError(selector, message) {
            $(selector).html(`
            <div class="col-span-7 text-center py-8">
                <div class="text-red-500 mb-2">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <p class="text-gray-600">${message}</p>
                <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-sky-600 text-white rounded-lg text-sm hover:bg-sky-700">
                    Retry
                </button>
            </div>
        `);
        }

        // Show appointment details by ID
        function showAppointmentDetailsById(appointmentId) {
            const modal = document.getElementById('appointmentModal');
            const modalContent = document.getElementById('appointmentModalContent');

            modal.classList.remove('hidden');

            modalContent.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                </div>
            `;

            const detailsUrl = '{{ route('doctor.appointments.details.json', ['id' => 'APPOINTMENT_ID']) }}'.replace(
                'APPOINTMENT_ID', appointmentId);

            fetch(detailsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        renderAppointmentDetails(data.data);
                    } else {
                        showAppointmentError('Failed to load appointment details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAppointmentError('An error occurred while loading the appointment.');
                });
        }

        function renderAppointmentDetails(data) {
            const apt = data.appointment;
            const patient = data.patient;

            const statusColors = {
                'confirmed': 'bg-green-100 text-green-800',
                'pending': 'bg-amber-100 text-amber-800',
                'completed': 'bg-sky-100 text-sky-800',
                'cancelled': 'bg-red-100 text-red-800'
            };

            const modalContent = document.getElementById('appointmentModalContent');

            function formatType(str) {
                return str
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }

            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">#${apt.appointment_number}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[apt.status] || 'bg-gray-100 text-gray-800'}">
                            ${apt.status.toUpperCase()}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Date</p>
                            <p class="text-sm font-semibold text-gray-800">${apt.date}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Time</p>
                            <p class="text-sm font-semibold text-gray-800">${apt.time}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Type</p>
                            <p class="text-sm font-semibold text-gray-800">${formatType(apt.type)}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Duration</p>
                            <p class="text-sm font-semibold text-gray-800">${apt.duration} minutes</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Patient Information 
                        </h4>
                        <div class="space-y-2 pl-7">
                            <p class="text-sm"><span class="font-medium text-gray-700">Name:</span> ${patient.name}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Age:</span> ${patient.age || 'N/A'} years</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Gender:</span> ${patient.gender}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Phone:</span> ${patient.phone || 'N/A'}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Email:</span> ${patient.email || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Appointment Details</h4>
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-medium text-gray-700">Reason:</span> ${apt.reason || 'Not specified'}</p>
                            ${apt.symptoms ? `<p class="text-sm"><span class="font-medium text-gray-700">Symptoms:</span> ${apt.symptoms}</p>` : ''}
                            ${apt.notes ? `<p class="text-sm"><span class="font-medium text-gray-700">Notes:</span> ${apt.notes}</p>` : ''}
                        </div>
                    </div>
                    
                    <div class="border-t pt-4 flex gap-3">
                        <a href="/doctor/appointment-details/${apt.id}" 
                           class="flex-1 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm font-medium text-center">
                            View Full Details
                        </a>
                        <button onclick="closeAppointmentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                            Close
                        </button>
                    </div>
                </div>
            `;
        }

        function showAppointmentError(message) {
            const modalContent = document.getElementById('appointmentModalContent');
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-600">${message}</p>
                    <button onclick="closeAppointmentModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Close
                    </button>
                </div>
            `;
        }

        function closeAppointmentModal() {
            document.getElementById('appointmentModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAppointmentModal();
            }
        });

        document.getElementById('dateAppointmentsModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDateModal();
            }
        });

        $(document).on('click', function(event) {
            if ($(event.target).hasClass('fixed') && $(event.target).hasClass('inset-0')) {
                closeModal();
            }
        });
    </script>
@endpush
