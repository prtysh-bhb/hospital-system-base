@extends('layouts.admin')

@section('title', 'Calendar')

@section('page-title', 'Appointments Calendar')

@section('header-actions')
    <form method="GET" action="{{ route('admin.calendar') }}" class="flex items-center gap-3">
        <select name="doctor_id" class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
            onchange="this.form.submit()">
            <option value="">All Doctors</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor['id'] }}" {{ $doctorId == $doctor['id'] ? 'selected' : '' }}>
                    {{ $doctor['name'] }} - {{ $doctor['specialty'] }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="year" value="{{ $calendarData['year'] }}">
        <input type="hidden" name="month" value="{{ $calendarData['month'] }}">
    </form>
    <a href="{{ route('admin.add-appointment') }}"
        class="px-6 py-2 text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">+ New Appointment</a>
@endsection

@section('content')
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
            @foreach ($calendarData['days'] as $day)
                @php
                    $colors = ['sky', 'purple', 'emerald', 'amber', 'rose', 'indigo', 'pink'];
                    $borderClass = in_array($loop->index % 7, [0, 1, 2, 3, 4, 5]) ? 'border-r' : '';
                    $bgClass = !$day['is_current_month'] ? 'bg-gray-50' : '';
                    $todayBg = $day['is_today'] ? 'bg-sky-50' : '';
                @endphp
                <div
                    class="p-4 {{ $borderClass }} border-b border-gray-100 h-32 {{ $bgClass }} {{ $todayBg }}">
                    <p
                        class="text-sm font-semibold mb-2 {{ !$day['is_current_month'] ? 'text-gray-400' : 'text-gray-800' }} {{ $day['is_today'] ? 'text-sky-700' : '' }}">
                        {{ $day['day'] }}
                        @if ($day['is_today'])
                            <span class="text-xs"> • Today</span>
                        @endif
                    </p>
                    <div class="space-y-1">
                        @foreach ($day['appointments'] as $index => $appointment)
                            @if ($index < 2)
                                @php
                                    $color = $colors[$index % count($colors)];
                                    $statusColors = [
                                        'confirmed' => 'emerald',
                                        'pending' => 'amber',
                                        'completed' => 'sky',
                                        'cancelled' => 'red',
                                    ];
                                    $statusColor = $statusColors[$appointment['status']] ?? 'gray';
                                @endphp
                                <div class="text-xs px-2 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 rounded truncate cursor-pointer hover:bg-{{ $statusColor }}-200"
                                    onclick="showAppointmentDetails({{ $appointment['id'] }})"
                                    title="{{ $appointment['time'] }} - {{ $appointment['patient_name'] }} with {{ $appointment['doctor_name'] }}">
                                    {{ $appointment['time'] }} {{ $appointment['doctor_short'] }}
                                </div>
                            @endif
                        @endforeach
                        @if (count($day['appointments']) > 2)
                            <div class="text-xs text-sky-600 px-2 cursor-pointer hover:text-sky-800 font-medium"
                                onclick="showDateAppointments('{{ $day['date'] }}')"
                                title="Click to see all appointments">
                                +{{ count($day['appointments']) - 2 }} more
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
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
                                d="M6 18L18 6M6 6l12 12">
                            </path>
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
                                d="M6 18L18 6M6 6l12 12">
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

    <script>
        // Helper function to parse time string to hour in 24-hour format
        function parseTimeToHour(timeStr) {
            if (!timeStr) return -1;

            // Handle formats like "9:00 AM", "10:30 PM", "09:00 AM"
            const match = timeStr.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
            if (!match) return -1;

            let hour = parseInt(match[1], 10);
            const period = match[3].toUpperCase();

            if (period === 'PM' && hour !== 12) hour += 12;
            if (period === 'AM' && hour === 12) hour = 0;

            return hour;
        }

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
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Patient Information
                                    </h4>
                                    <div class="space-y-2 pl-7">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Name:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.patient_name}</span>
                                        </div>
                                        ${apt.patient_phone ? `
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Phone:</span>
                                                            <span class="text-sm font-medium text-gray-800">${apt.patient_phone}</span>
                                                        </div>
                                                        ` : ''}
                                        ${apt.patient_email ? `
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Email:</span>
                                                            <span class="text-sm font-medium text-gray-800">${apt.patient_email}</span>
                                                        </div>
                                                        ` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Doctor Information
                                    </h4>
                                    <div class="space-y-2 pl-7">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Name:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.doctor_name}</span>
                                        </div>
                                        ${apt.doctor_specialty ? `
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Specialty:</span>
                                                            <span class="text-sm font-medium text-gray-800">${apt.doctor_specialty}</span>
                                                        </div>
                                                        ` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Appointment Details</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Type:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.appointment_type.replace('_', ' ').toUpperCase()}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Duration:</span>
                                            <span class="text-sm font-medium text-gray-800">${apt.duration_minutes} minutes</span>
                                        </div>
                                        ${apt.reason_for_visit ? `
                                                        <div>
                                                            <span class="text-sm text-gray-600">Reason:</span>
                                                            <p class="text-sm font-medium text-gray-800 mt-1">${apt.reason_for_visit}</p>
                                                        </div>
                                                        ` : ''}
                                        ${apt.symptoms ? `
                                                        <div>
                                                            <span class="text-sm text-gray-600">Symptoms:</span>
                                                            <p class="text-sm font-medium text-gray-800 mt-1">${apt.symptoms}</p>
                                                        </div>
                                                        ` : ''}
                                        ${apt.notes ? `
                                                        <div>
                                                            <span class="text-sm text-gray-600">Notes:</span>
                                                            <p class="text-sm font-medium text-gray-800 mt-1">${apt.notes}</p>
                                                        </div>
                                                        ` : ''}
                                    </div>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <button onclick="closeModal()" class="w-full px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm font-medium">
                                        Close
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        modalContent.innerHTML = `
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600">Failed to load appointment details.</p>
                                <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    Close
                                </button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600">An error occurred while loading the appointment.</p>
                            <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Close
                            </button>
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

            const doctorId = '{{ $doctorId ?? '' }}';
            const url = `/admin/calendar/appointments?date=${date}${doctorId ? '&doctor_id=' + doctorId : ''}`;

            fetch(url)
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
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-gray-700 font-medium">${apt.patient_name}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-gray-700">${apt.doctor_name}</span>
                                            ${apt.specialty ? `<span class="text-gray-500 ml-1">(${apt.specialty})</span>` : ''}
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

        document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('dateAppointmentsModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDateModal();
            }
        });

        // View switching and navigation
        let currentView = 'month';
        let currentYear = {{ $calendarData['year'] }};
        let currentMonth = {{ $calendarData['month'] }};
        let currentDate = new Date();
        let currentDoctorId = '{{ $doctorId ?? '' }}';
        let originalMonthName = '{{ $calendarData['month_name'] }}';
        let originalStatistics = {
            total: {{ $statistics['total'] }},
            confirmed: {{ $statistics['confirmed'] }},
            pending: {{ $statistics['pending'] }},
            completed: {{ $statistics['completed'] }},
            cancelled: {{ $statistics['cancelled'] }}
        };

        // Function to update statistics cards
        function updateStatistics(statistics) {
            if (statistics) {
                document.getElementById('statTotal').textContent = statistics.total || 0;
                document.getElementById('statConfirmed').textContent = statistics.confirmed || 0;
                document.getElementById('statPending').textContent = statistics.pending || 0;
                document.getElementById('statCompleted').textContent = statistics.completed || 0;
                document.getElementById('statCancelled').textContent = statistics.cancelled || 0;
            }
        }

        // Initialize calendar grid with server data
        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = `@foreach ($calendarData['days'] as $day)
                @php
                    $colors = ['sky', 'purple', 'emerald', 'amber', 'rose', 'indigo', 'pink'];
                    $borderClass = in_array($loop->index % 7, [0, 1, 2, 3, 4, 5]) ? 'border-r' : '';
                    $bgClass = !$day['is_current_month'] ? 'bg-gray-50' : '';
                    $todayBg = $day['is_today'] ? 'bg-sky-50' : '';
                @endphp
                <div
                    class="p-4 {{ $borderClass }} border-b border-gray-100 h-32 {{ $bgClass }} {{ $todayBg }}">
                    <p
                        class="text-sm font-semibold mb-2 {{ !$day['is_current_month'] ? 'text-gray-400' : 'text-gray-800' }} {{ $day['is_today'] ? 'text-sky-700' : '' }}">
                        {{ $day['day'] }}
                        @if ($day['is_today'])
                            <span class="text-xs"> • Today</span>
                        @endif
                    </p>
                    <div class="space-y-1">
                        @foreach ($day['appointments'] as $index => $appointment)
                            @if ($index < 2)
                                @php
                                    $color = $colors[$index % count($colors)];
                                    $statusColors = [
                                        'confirmed' => 'emerald',
                                        'pending' => 'amber',
                                        'completed' => 'sky',
                                        'cancelled' => 'red',
                                    ];
                                    $statusColor = $statusColors[$appointment['status']] ?? 'gray';
                                @endphp
                                <div class="text-xs px-2 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 rounded truncate cursor-pointer hover:bg-{{ $statusColor }}-200"
                                    onclick="showAppointmentDetails({{ $appointment['id'] }})"
                                    title="{{ $appointment['time'] }} - {{ $appointment['patient_name'] }} with {{ $appointment['doctor_name'] }}">
                                    {{ $appointment['time'] }} {{ $appointment['doctor_short'] }}
                                </div>
                            @endif
                        @endforeach
                        @if (count($day['appointments']) > 2)
                            <div class="text-xs text-sky-600 px-2 cursor-pointer hover:text-sky-800 font-medium"
                                onclick="showDateAppointments('{{ $day['date'] }}')"
                                title="Click to see all appointments">
                                +{{ count($day['appointments']) - 2 }} more
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach`;

        // View buttons
        document.getElementById('viewMonth').addEventListener('click', () => switchView('month'));
        document.getElementById('viewWeek').addEventListener('click', () => switchView('week'));
        document.getElementById('viewDay').addEventListener('click', () => switchView('day'));

        // Navigation buttons
        document.getElementById('prevPeriod').addEventListener('click', navigatePrev);
        document.getElementById('nextPeriod').addEventListener('click', navigateNext);

        function switchView(view) {
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
            if (view === 'week') {
                loadWeekView();
            } else if (view === 'day') {
                loadDayView();
            } else {
                document.getElementById('periodTitle').textContent = originalMonthName;
                updateStatistics(originalStatistics);
            }
        }

        function navigatePrev() {
            if (currentView === 'month') {
                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
                window.location.href =
                    `{{ route('admin.calendar') }}?year=${currentYear}&month=${currentMonth}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;
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
                window.location.href =
                    `{{ route('admin.calendar') }}?year=${currentYear}&month=${currentMonth}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`;
            } else if (currentView === 'week') {
                currentDate.setDate(currentDate.getDate() + 7);
                loadWeekView();
            } else if (currentView === 'day') {
                currentDate.setDate(currentDate.getDate() + 1);
                loadDayView();
            }
        }

        function loadWeekView() {
            const startOfWeek = new Date(currentDate);
            let day = currentDate.getDay();
            if (day === 0) day = 7;
            startOfWeek.setDate(currentDate.getDate() - day + 1);
            const startDate = startOfWeek.toISOString().split('T')[0];

            document.getElementById('weekView').innerHTML =
                '<div class="text-center py-8"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600 mx-auto"></div></div>';

            fetch(
                `{{ route('admin.calendar.week') }}?start_date=${startDate}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`
            )
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('periodTitle').textContent = data.data.week_title;
                    renderWeekView(data.data.days);
                    if (data.statistics) {
                        updateStatistics(data.statistics);
                    }

                    // highlight current day
                    setTimeout(() => {
                        const today = new Date().toISOString().split('T')[0];
                        const todayCell = document.querySelector(`[data-date="${today}"]`);
                        if (todayCell) {
                            todayCell.classList.add('active-day');
                        }
                    }, 50);
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
                    <!-- Week Header -->
                    <div class="grid grid-cols-8 border-b border-gray-200 bg-gray-50">
                        <div class="p-4 border-r border-gray-200">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Time</p>
                        </div>
            `;

            // Add day headers
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

                // Parse the slot hour
                const slotHour = parseTimeToHour(timeSlot);

                // Day columns
                days.forEach(day => {
                    const isToday = day.is_today;
                    const dayAppointments = (day.appointments || []).filter(apt => {
                        // Check if appointment falls within this hour slot
                        const aptHour = parseTimeToHour(apt.time);
                        return aptHour === slotHour;
                    });

                    html += `
                        <div class="p-2 border-r border-gray-100 min-h-20 ${isToday ? 'bg-sky-50' : ''}">
                    `;

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

        function loadDayView() {
            const dateStr = currentDate.toISOString().split('T')[0];

            document.getElementById('dayView').innerHTML =
                '<div class="text-center py-8"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600 mx-auto"></div></div>';

            fetch(
                    `{{ route('admin.calendar.day') }}?date=${dateStr}${currentDoctorId ? '&doctor_id=' + currentDoctorId : ''}`
                    )
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('periodTitle').textContent = data.data.date_title;
                        renderDayView(data.data);
                        if (data.statistics) {
                            updateStatistics(data.statistics);
                        }
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
                    <!-- Day Header -->
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-xl font-semibold text-gray-800">${dayData.date_title}</h3>
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
                // Parse the slot hour
                const slotHour = parseTimeToHour(timeSlot);

                const slotAppointments = (dayData.appointments || []).filter(apt => {
                    // Check if appointment falls within this hour slot
                    const aptHour = parseTimeToHour(apt.time);
                    return aptHour === slotHour;
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

        // Close modals when clicking outside
        document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('dateAppointmentsModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDateModal();
            }
        });
    </script>
@endsection
