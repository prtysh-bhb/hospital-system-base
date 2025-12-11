@extends('layouts.admin')

@section('title', 'Calendar')

@section('page-title', 'Appointments Calendar')

@section('header-actions')
    <a href="{{ route('admin.add-appointment') }}"
        class="px-6 py-2 text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">+ New Appointment</a>
@endsection

@section('content')
    <!-- Search & Filter Card -->
    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Doctor</label>
                <select id="doctorFilter"
                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor['id'] }}" {{ $doctorId == $doctor['id'] ? 'selected' : '' }}>
                            {{ $doctor['name'] }} - {{ $doctor['specialty'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jump to Date</label>
                <input type="date" id="jumpToDate"
                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    value="{{ date('Y-m-d') }}">
            </div>
            <div class="flex items-end">
                <button id="todayBtn"
                    class="w-full px-4 py-2 text-sm text-sky-600 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 font-medium">
                    Today
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Controls -->
    <div class="bg-white p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
            <div class="flex items-center space-x-2 sm:space-x-4">
                <button id="prevPeriod" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 id="periodTitle" class="text-base sm:text-lg font-semibold text-gray-800">
                    {{ $calendarData['month_name'] }}</h3>
                <button id="nextPeriod" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div class="flex gap-2">
                <button id="viewMonth"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-sky-600 rounded-lg">Month</button>
                <button id="viewWeek"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Week</button>
                <button id="viewDay"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Day</button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase">Total</p>
            <p id="statTotal" class="text-2xl font-bold text-gray-800">{{ $statistics['total'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase">Confirmed</p>
            <p id="statConfirmed" class="text-2xl font-bold text-green-600">{{ $statistics['confirmed'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase">Pending</p>
            <p id="statPending" class="text-2xl font-bold text-amber-600">{{ $statistics['pending'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase">Completed</p>
            <p id="statCompleted" class="text-2xl font-bold text-sky-600">{{ $statistics['completed'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase">Cancelled</p>
            <p id="statCancelled" class="text-2xl font-bold text-red-600">{{ $statistics['cancelled'] }}</p>
        </div>
    </div>

    <!-- Month Calendar View -->
    <div id="monthView" class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 border-b border-gray-200">
            <div class="p-2 sm:p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Sun</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Mon</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Tue</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Wed</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Thu</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Fri</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs font-semibold text-gray-600 uppercase">Sat</p>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7" id="calendarGrid">
            <div class="col-span-7 text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                <p class="mt-2 text-gray-500">Loading calendar...</p>
            </div>
        </div>
    </div>

    <!-- Week View -->
    <div id="weekView" class="hidden bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6"></div>

    <!-- Day View -->
    <div id="dayView" class="hidden bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6"></div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Appointment Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="p-6">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
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

    <script>
        // State variables
        let currentView = 'month';
        let currentYear = {{ $calendarData['year'] }};
        let currentMonth = {{ $calendarData['month'] }};
        let currentDate = new Date(currentYear, currentMonth - 1, 1);
        let currentDoctorId = '{{ $doctorId ?? '' }}';

        // Helper function to parse time string to hour in 24-hour format
        function parseTimeToHour(timeStr) {
            if (!timeStr) return -1;
            const match = timeStr.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
            if (!match) return -1;
            let hour = parseInt(match[1], 10);
            const period = match[3].toUpperCase();
            if (period === 'PM' && hour !== 12) hour += 12;
            if (period === 'AM' && hour === 12) hour = 0;
            return hour;
        }

        // Update URL without page refresh
        function updateURL() {
            const params = new URLSearchParams();
            params.set('view', currentView);

            if (currentView === 'month') {
                params.set('year', currentYear);
                params.set('month', currentMonth);
                // Calculate from and to dates for month
                const firstDay = new Date(currentYear, currentMonth - 1, 1);
                const lastDay = new Date(currentYear, currentMonth, 0);
                params.set('from', firstDay.toISOString().split('T')[0]);
                params.set('to', lastDay.toISOString().split('T')[0]);
            } else if (currentView === 'week') {
                const startOfWeek = getStartOfWeek(currentDate);
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                params.set('from', startOfWeek.toISOString().split('T')[0]);
                params.set('to', endOfWeek.toISOString().split('T')[0]);
            } else if (currentView === 'day') {
                const dateStr = currentDate.toISOString().split('T')[0];
                params.set('date', dateStr);
                params.set('from', dateStr);
                params.set('to', dateStr);
            }

            if (currentDoctorId) {
                params.set('doctor_id', currentDoctorId);
            }

            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({
                path: newUrl
            }, '', newUrl);
        }

        // Get start of week (Sunday)
        function getStartOfWeek(date) {
            const d = new Date(date);
            const day = d.getDay();
            d.setDate(d.getDate() - day);
            return d;
        }

        // Update statistics cards
        function updateStatistics(statistics) {
            if (statistics) {
                document.getElementById('statTotal').textContent = statistics.total || 0;
                document.getElementById('statConfirmed').textContent = statistics.confirmed || 0;
                document.getElementById('statPending').textContent = statistics.pending || 0;
                document.getElementById('statCompleted').textContent = statistics.completed || 0;
                document.getElementById('statCancelled').textContent = statistics.cancelled || 0;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial view from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const viewParam = urlParams.get('view');
            if (viewParam && ['month', 'week', 'day'].includes(viewParam)) {
                currentView = viewParam;
            }

            // Set doctor from URL
            const doctorParam = urlParams.get('doctor_id');
            if (doctorParam) {
                currentDoctorId = doctorParam;
                document.getElementById('doctorFilter').value = doctorParam;
            }

            // Set date from URL params
            const fromParam = urlParams.get('from');
            if (fromParam) {
                const parsedDate = new Date(fromParam);
                if (!isNaN(parsedDate.getTime())) {
                    currentDate = parsedDate;
                    currentYear = parsedDate.getFullYear();
                    currentMonth = parsedDate.getMonth() + 1;
                    document.getElementById('jumpToDate').value = fromParam;
                }
            }

            // Load the appropriate view
            switchView(currentView, false);

            // Event listeners
            document.getElementById('viewMonth').addEventListener('click', () => switchView('month'));
            document.getElementById('viewWeek').addEventListener('click', () => switchView('week'));
            document.getElementById('viewDay').addEventListener('click', () => switchView('day'));
            document.getElementById('prevPeriod').addEventListener('click', navigatePrev);
            document.getElementById('nextPeriod').addEventListener('click', navigateNext);

            // Doctor filter change
            document.getElementById('doctorFilter').addEventListener('change', function() {
                currentDoctorId = this.value;
                reloadCurrentView();
            });

            // Jump to date
            document.getElementById('jumpToDate').addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                currentDate = selectedDate;
                currentYear = selectedDate.getFullYear();
                currentMonth = selectedDate.getMonth() + 1;
                reloadCurrentView();
            });

            // Today button
            document.getElementById('todayBtn').addEventListener('click', function() {
                const today = new Date();
                currentDate = today;
                currentYear = today.getFullYear();
                currentMonth = today.getMonth() + 1;
                document.getElementById('jumpToDate').value = today.toISOString().split('T')[0];
                reloadCurrentView();
            });
        });

        function reloadCurrentView() {
            if (currentView === 'month') {
                loadMonthView();
            } else if (currentView === 'week') {
                loadWeekView();
            } else if (currentView === 'day') {
                loadDayView();
            }
        }

        function switchView(view, updateUrl = true) {
            currentView = view;

            // Update button styles
            document.getElementById('viewMonth').className = 'px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ' +
                (view === 'month' ? 'text-white bg-sky-600' :
                    'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50') + ' rounded-lg';
            document.getElementById('viewWeek').className = 'px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ' +
                (view === 'week' ? 'text-white bg-sky-600' :
                    'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50') + ' rounded-lg';
            document.getElementById('viewDay').className = 'px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ' +
                (view === 'day' ? 'text-white bg-sky-600' :
                    'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50') + ' rounded-lg';

            // Show/hide views
            document.getElementById('monthView').classList.toggle('hidden', view !== 'month');
            document.getElementById('weekView').classList.toggle('hidden', view !== 'week');
            document.getElementById('dayView').classList.toggle('hidden', view !== 'day');

            // Load appropriate view
            if (view === 'month') {
                loadMonthView();
            } else if (view === 'week') {
                loadWeekView();
            } else if (view === 'day') {
                loadDayView();
            }

            if (updateUrl) {
                updateURL();
            }
        }

        function navigatePrev() {
            if (currentView === 'month') {
                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
                currentDate = new Date(currentYear, currentMonth - 1, 1);
                loadMonthView();
            } else if (currentView === 'week') {
                currentDate.setDate(currentDate.getDate() - 7);
                loadWeekView();
            } else if (currentView === 'day') {
                currentDate.setDate(currentDate.getDate() - 1);
                loadDayView();
            }
        }

        function navigateNext() {
            if (currentView === 'month') {
                currentMonth++;
                if (currentMonth > 12) {
                    currentMonth = 1;
                    currentYear++;
                }
                currentDate = new Date(currentYear, currentMonth - 1, 1);
                loadMonthView();
            } else if (currentView === 'week') {
                currentDate.setDate(currentDate.getDate() + 7);
                loadWeekView();
            } else if (currentView === 'day') {
                currentDate.setDate(currentDate.getDate() + 1);
                loadDayView();
            }
        }

        // AJAX Load Month View
        function loadMonthView() {
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = `
                <div class="col-span-7 text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                    <p class="mt-2 text-gray-500">Loading calendar...</p>
                </div>
            `;

            const url =
                `{{ route('admin.calendar.month') }}?year=${currentYear}&month=${currentMonth}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('periodTitle').textContent = data.data.month_name;
                        renderMonthView(data.data.days);
                        updateStatistics(data.statistics);
                        updateURL();
                    } else {
                        calendarGrid.innerHTML =
                            `<div class="col-span-7 text-center py-8 text-red-500">Failed to load calendar</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading month view:', error);
                    calendarGrid.innerHTML =
                        `<div class="col-span-7 text-center py-8 text-red-500">Error loading calendar</div>`;
                });
        }

        function renderMonthView(days) {
            const statusColors = {
                'confirmed': 'emerald',
                'pending': 'amber',
                'completed': 'sky',
                'cancelled': 'red'
            };

            let html = '';
            days.forEach((day, index) => {
                const borderClass = (index % 7 !== 6) ? 'border-r' : '';
                const bgClass = !day.is_current_month ? 'bg-gray-50' : '';
                const todayBg = day.is_today ? 'bg-sky-50' : '';
                const textClass = !day.is_current_month ? 'text-gray-400' : 'text-gray-800';
                const todayText = day.is_today ? 'text-sky-700' : '';

                html += `<div class="p-4 ${borderClass} border-b border-gray-100 h-32 ${bgClass} ${todayBg}">`;
                html += `<p class="text-sm font-semibold mb-2 ${textClass} ${todayText}">`;
                html += day.day;
                if (day.is_today) {
                    html += ' <span class="text-xs">• Today</span>';
                }
                html += '</p>';
                html += '<div class="space-y-1">';

                if (day.appointments && day.appointments.length > 0) {
                    day.appointments.slice(0, 2).forEach(apt => {
                        const statusColor = statusColors[apt.status] || 'gray';
                        html += `
                            <div class="text-xs px-2 py-1 bg-${statusColor}-100 text-${statusColor}-700 rounded truncate cursor-pointer hover:bg-${statusColor}-200"
                                onclick="showAppointmentDetails(${apt.id})"
                                title="${apt.time} - ${apt.patient_name} with ${apt.doctor_name}">
                                ${apt.time} ${apt.doctor_short || ''}
                            </div>
                        `;
                    });

                    if (day.appointments.length > 2) {
                        html += `
                            <div class="text-xs text-sky-600 px-2 cursor-pointer hover:text-sky-800 font-medium"
                                onclick="showDateAppointments('${day.date}')"
                                title="Click to see all appointments">
                                +${day.appointments.length - 2} more
                            </div>
                        `;
                    }
                }

                html += '</div></div>';
            });

            document.getElementById('calendarGrid').innerHTML = html;
        }

        // AJAX Load Week View
        function loadWeekView() {
            const startOfWeek = getStartOfWeek(currentDate);
            const startDate = startOfWeek.toISOString().split('T')[0];

            document.getElementById('weekView').innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading week view...</p>
                </div>
            `;

            const url =
                `{{ route('admin.calendar.week') }}?start_date=${startDate}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('periodTitle').textContent = data.data.week_title;
                        renderWeekView(data.data.days);
                        updateStatistics(data.statistics);
                        updateURL();
                    }
                })
                .catch(error => console.error('Error loading week view:', error));
        }

        function renderWeekView(days) {
            const statusColors = {
                'confirmed': 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'pending': 'bg-amber-100 text-amber-700 border-amber-200',
                'completed': 'bg-sky-100 text-sky-700 border-sky-200',
                'cancelled': 'bg-red-100 text-red-700 border-red-200'
            };

            let html = `
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="grid grid-cols-8 border-b border-gray-200 bg-gray-50">
                        <div class="p-4 border-r border-gray-200">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Time</p>
                        </div>
            `;

            days.forEach(day => {
                const isToday = day.is_today;
                html += `
                    <div class="p-4 text-center border-r border-gray-200 ${isToday ? 'bg-sky-50' : ''}">
                        <p class="text-xs font-semibold text-gray-500 uppercase">${day.day_short || ''}</p>
                        <p class="text-lg font-bold ${isToday ? 'text-sky-600' : 'text-gray-800'}">${day.day_num || ''}</p>
                        <p class="text-xs text-gray-500">${day.month_short || ''}</p>
                    </div>
                `;
            });

            html += `</div>`;

            // 24-hour time slots (00:00 to 23:00)
            const timeSlots = [];
            for (let hour = 0; hour < 24; hour++) {
                timeSlots.push({
                    display: `${String(hour).padStart(2, '0')}:00`,
                    hour: hour
                });
            }

            timeSlots.forEach(slot => {
                html += `<div class="grid grid-cols-8 border-b border-gray-100">`;
                html +=
                    `<div class="p-4 border-r border-gray-200 bg-gray-50"><p class="text-sm font-medium text-gray-600">${slot.display}</p></div>`;

                const slotHour = slot.hour;

                days.forEach(day => {
                    const isToday = day.is_today;
                    const dayAppointments = (day.appointments || []).filter(apt => parseTimeToHour(apt
                        .time) === slotHour);

                    html +=
                        `<div class="p-2 border-r border-gray-100 min-h-20 ${isToday ? 'bg-sky-50' : ''}">`;

                    if (dayAppointments.length > 0) {
                        dayAppointments.forEach(apt => {
                            const statusClass = statusColors[apt.status] ||
                                'bg-gray-100 text-gray-700';
                            html += `
                                <div class="text-xs p-2 mb-1 ${statusClass} rounded cursor-pointer hover:shadow-sm border"
                                    onclick="showAppointmentDetails(${apt.id})"
                                    title="${apt.time || ''} - ${apt.patient_name || 'N/A'}">
                                    <div class="font-semibold truncate">${apt.patient_name ? apt.patient_name.split(' ')[0] : 'N/A'}</div>
                                    <div class="text-gray-600 truncate">${apt.doctor_short || ''}</div>
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
            document.getElementById('weekView').innerHTML = html;
        }

        // AJAX Load Day View
        function loadDayView() {
            const dateStr = currentDate.toISOString().split('T')[0];

            document.getElementById('dayView').innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading day view...</p>
                </div>
            `;

            const url =
                `{{ route('admin.calendar.day') }}?date=${dateStr}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('periodTitle').textContent = data.data.date_title;
                        renderDayView(data.data);
                        updateStatistics(data.statistics);
                        updateURL();
                    }
                })
                .catch(error => console.error('Error loading day view:', error));
        }

        function renderDayView(dayData) {
            const statusColors = {
                'confirmed': 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'pending': 'bg-amber-100 text-amber-700 border-amber-200',
                'completed': 'bg-sky-100 text-sky-700 border-sky-200',
                'cancelled': 'bg-red-100 text-red-700 border-red-200'
            };

            let html = `
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-xl font-semibold text-gray-800">${dayData.date_title}</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
            `;

            // 24-hour time slots (00:00 to 23:00)
            const timeSlots = [];
            for (let hour = 0; hour < 24; hour++) {
                timeSlots.push({
                    display: `${String(hour).padStart(2, '0')}:00`,
                    hour: hour
                });
            }

            timeSlots.forEach(slot => {
                const slotHour = slot.hour;
                const slotAppointments = (dayData.appointments || []).filter(apt => parseTimeToHour(apt.time) ===
                    slotHour);

                html += `
                    <div class="grid grid-cols-12 p-4 hover:bg-gray-50 transition-colors">
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">${slot.display}</p>
                        </div>
                        <div class="col-span-10">
                `;

                if (slotAppointments.length > 0) {
                    slotAppointments.forEach(apt => {
                        const statusClass = statusColors[apt.status] || 'bg-gray-100 text-gray-700';
                        html += `
                            <div class="mb-3 p-4 ${statusClass} rounded-lg border cursor-pointer hover:shadow-sm transition-shadow"
                                onclick="showAppointmentDetails(${apt.id})">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-semibold text-gray-800">${apt.patient_name || 'N/A'}</span>
                                            <span class="text-xs px-2 py-1 rounded-full ${statusClass} border">${(apt.status || '').toUpperCase()}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">${apt.doctor_name || 'N/A'}</span> • ${apt.specialty || 'General'}
                                        </div>
                                        ${apt.reason ? `<div class="text-sm text-gray-500 mt-2">${apt.reason}</div>` : ''}
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-800">${apt.duration || 30} min</div>
                                        <div class="text-xs text-gray-500">${apt.appointment_number || ''}</div>
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
            document.getElementById('dayView').innerHTML = html;
        }

        // Show appointment details modal
        function showAppointmentDetails(appointmentId) {
            const modal = document.getElementById('appointmentModal');
            const modalContent = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modalContent.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                </div>
            `;

            fetch(`/admin/appointments/${appointmentId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const apt = data.appointment;
                        const statusColors = {
                            'confirmed': 'bg-green-100 text-green-800',
                            'pending': 'bg-amber-100 text-amber-800',
                            'completed': 'bg-sky-100 text-sky-800',
                            'cancelled': 'bg-red-100 text-red-800'
                        };

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
                                        <p class="text-sm font-semibold text-gray-800">${apt.formatted_date}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase mb-1">Time</p>
                                        <p class="text-sm font-semibold text-gray-800">${apt.formatted_time}</p>
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Patient Information</h4>
                                    <div class="space-y-2 pl-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Name:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.patient_name}</span>
                                        </div>
                                        ${apt.patient_phone ? `<div class="flex justify-between"><span class="text-sm text-gray-600">Phone:</span><span class="text-sm font-medium text-gray-800">${apt.patient_phone}</span></div>` : ''}
                                        ${apt.patient_email ? `<div class="flex justify-between"><span class="text-sm text-gray-600">Email:</span><span class="text-sm font-medium text-gray-800">${apt.patient_email}</span></div>` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Doctor Information</h4>
                                    <div class="space-y-2 pl-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Name:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.doctor_name}</span>
                                        </div>
                                        ${apt.doctor_specialty ? `<div class="flex justify-between"><span class="text-sm text-gray-600">Specialty:</span><span class="text-sm font-medium text-gray-800">${apt.doctor_specialty}</span></div>` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Appointment Details</h4>
                                    <div class="space-y-2 pl-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Type:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.appointment_type.replace('_', ' ').toUpperCase()}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Duration:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.duration_minutes} minutes</span>
                                        </div>
                                        ${apt.reason_for_visit ? `<div><span class="text-sm text-gray-600">Reason:</span><p class="text-sm font-medium text-gray-800 mt-1">${apt.reason_for_visit}</p></div>` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <button onclick="closeModal()" class="w-full px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm font-medium">Close</button>
                                </div>
                            </div>
                        `;
                    } else {
                        modalContent.innerHTML = `
                            <div class="text-center py-8">
                                <p class="text-red-600">Failed to load appointment details.</p>
                                <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Close</button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-600">An error occurred while loading the appointment.</p>
                            <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Close</button>
                        </div>
                    `;
                });
        }

        function closeModal() {
            document.getElementById('appointmentModal').classList.add('hidden');
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

            const url =
                `/admin/calendar/appointments?date=${date}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalTitle.textContent = `Appointments for ${data.date}`;

                        if (data.appointments.length === 0) {
                            modalContent.innerHTML = `
                                <div class="text-center py-8">
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
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" onclick="closeDateModal(); showAppointmentDetails(${apt.id})">
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
                                            <span class="text-gray-700 font-medium">${apt.patient_name}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="text-gray-700">${apt.doctor_name}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('');

                            modalContent.innerHTML =
                                `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">${appointmentsHtml}</div>`;
                        }
                    } else {
                        modalContent.innerHTML =
                            `<div class="text-center py-8"><p class="text-red-600">Failed to load appointments.</p></div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML =
                        `<div class="text-center py-8"><p class="text-red-600">An error occurred.</p></div>`;
                });
        }

        function closeDateModal() {
            document.getElementById('dateAppointmentsModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('dateAppointmentsModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeDateModal();
        });
    </script>
@endsection
