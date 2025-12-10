@extends('layouts.frontdesk')

@section('title', 'Frontdesk Dashboard')

@section('page-title', 'Frontdesk Dashboard')

@section('content')
    <!-- Quick Actions -->
    <div class="mb-6 sm:mb-8">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('frontdesk.add-appointment') }}"
                class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-sky-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1 text-sm sm:text-base">Add Appointment</h4>
                <p class="text-xs sm:text-sm text-gray-600">Book new appointment</p>
            </a>

            <a href="{{ route('frontdesk.doctor-schedule') }}"
                class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1 text-sm sm:text-base">Doctor Schedule</h4>
                <p class="text-xs sm:text-sm text-gray-600">View availability</p>
            </a>

            <a href="{{ route('frontdesk.patients') }}"
                class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1 text-sm sm:text-base">Patients</h4>
                <p class="text-xs sm:text-sm text-gray-600">View patient records</p>
            </a>

            <a href="{{ route('frontdesk.history') }}"
                class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1 text-sm sm:text-base">History</h4>
                <p class="text-xs sm:text-sm text-gray-600">View past appointments</p>
            </a>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Today's Appointments</h3>
            <span class="text-xs sm:text-sm text-gray-500" id="current-date">Loading...</span>
        </div>

        <div id="appointments-container" class="space-y-3 sm:space-y-4">
            <!-- Loading State -->
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                <p class="text-sm text-gray-500 mt-2">Loading appointments...</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs sm:text-sm text-gray-500 mb-2">Today's Total</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" id="stat-today-total">
                <span class="inline-block animate-pulse bg-gray-200 rounded w-12 h-8"></span>
            </p>
            <p class="text-xs sm:text-sm text-green-600 mt-2">Appointments</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs sm:text-sm text-gray-500 mb-2">Waiting</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" id="stat-waiting">
                <span class="inline-block animate-pulse bg-gray-200 rounded w-12 h-8"></span>
            </p>
            <p class="text-xs sm:text-sm text-amber-600 mt-2">Patients in queue</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs sm:text-sm text-gray-500 mb-2">Available Doctors</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" id="stat-available-doctors">
                <span class="inline-block animate-pulse bg-gray-200 rounded w-12 h-8"></span>
            </p>
            <p class="text-xs sm:text-sm text-sky-600 mt-2">Currently on duty</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Recent Activity</h3>
            <span class="text-xs text-gray-400 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Live updates every 10s
            </span>
        </div>

        <div id="activity-container" class="space-y-3">
            <!-- Loading State -->
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                <p class="text-sm text-gray-500 mt-2">Loading recent activity...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();

            // Refresh every 10 seconds
            setInterval(loadDashboardData, 10000);
        });

        function loadDashboardData() {
            fetch('{{ route('frontdesk.dashboard.stats') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateAppointments(data.appointments);
                        updateStats(data.stats);
                        updateDate(data.current_date);
                        updateRecentActivities(data.recent_activities);
                    }
                })
                .catch(error => {
                    console.error('Error loading dashboard data:', error);
                    showError();
                });
        }

        function updateAppointments(appointments) {
            const container = document.getElementById('appointments-container');

            if (appointments.length === 0) {
                container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">No appointments scheduled for today</p>
                </div>
            `;
                return;
            }

            container.innerHTML = appointments.map(apt => `
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center gap-3 sm:gap-4 flex-1">
                    <div class="w-1.5 sm:w-2 h-10 sm:h-12 bg-${apt.status_color}-500 rounded-full flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm sm:text-base">${apt.time}</p>
                        <p class="text-xs sm:text-sm text-gray-600">${apt.patient_name}</p>
                    </div>
                </div>
                <div class="sm:text-center">
                    <p class="text-xs sm:text-sm font-medium text-gray-800">${apt.doctor_name}</p>
                    <p class="text-xs text-gray-500">${apt.doctor_specialty}</p>
                </div>
                <span class="px-2 sm:px-3 py-1 bg-${apt.status_color}-100 text-${apt.status_color}-700 text-xs font-semibold rounded-full self-start sm:self-center capitalize">
                    ${apt.status.replace('_', ' ')}
                </span>
            </div>
        `).join('');
        }

        function updateStats(stats) {
            document.getElementById('stat-today-total').textContent = stats.today_total;
            document.getElementById('stat-waiting').textContent = stats.waiting;
            document.getElementById('stat-available-doctors').textContent = stats.available_doctors;
        }

        function updateDate(date) {
            document.getElementById('current-date').textContent = date;
        }

        function updateRecentActivities(activities) {
            const container = document.getElementById('activity-container');

            if (!activities || activities.length === 0) {
                container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 text-sm">No recent activity</p>
                </div>
            `;
                return;
            }

            container.innerHTML = activities.map(activity => `
            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="w-8 h-8 bg-${activity.color}-100 rounded-full flex items-center justify-center flex-shrink-0">
                    ${getActivityIcon(activity.icon, activity.color)}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800">${activity.description}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500">${activity.time_ago}</span>
                        ${activity.user_name ? `<span class="text-xs text-gray-400">by ${activity.user_name}</span>` : ''}
                    </div>
                </div>
                <span class="px-2 py-0.5 bg-${activity.color}-100 text-${activity.color}-700 text-xs font-medium rounded capitalize flex-shrink-0">
                    ${activity.action.replace('_', ' ')}
                </span>
            </div>
        `).join('');
        }

        function getActivityIcon(icon, color) {
            const icons = {
                'plus-circle': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
                'pencil': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>`,
                'trash': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>`,
                'refresh': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>`,
                'login': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>`,
                'logout': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>`,
                'information-circle': `<svg class="w-4 h-4 text-${color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
            };
            return icons[icon] || icons['information-circle'];
        }

        function showError() {
            const container = document.getElementById('appointments-container');
            container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-500 mb-2">Failed to load appointments</p>
                <button onclick="loadDashboardData()" class="text-sky-600 hover:text-sky-700 text-sm font-medium">
                    Try Again
                </button>
            </div>
        `;
        }
    </script>
@endpush
