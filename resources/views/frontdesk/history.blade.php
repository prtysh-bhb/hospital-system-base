@extends('layouts.frontdesk')

@section('title', 'Appointment History')

@section('page-title', 'Appointment History')

@section('header-actions')
    <button onclick="exportReport()"
        class="px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm sm:text-base">
        Export Report
    </button>
@endsection

@section('content')
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="from_date" max="{{ date('Y-m-d') }}"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" id="to_date" max="{{ date('Y-m-d') }}"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status_filter"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                    <option value="all">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No-Show</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="search_input" placeholder="Patient or Doctor name..."
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div class="flex items-end">
                <button onclick="loadHistory(1)"
                    class="w-full px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6" id="statistics-cards">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Total Appointments</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2" id="stat-total">0</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Completed</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-2" id="stat-completed">0</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-3" id="stat-completed-rate">0% success rate</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Cancelled</p>
                    <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-2" id="stat-cancelled">0</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-3" id="stat-cancelled-rate">0% cancellation</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">No-Show</p>
                    <p class="text-2xl sm:text-3xl font-bold text-orange-600 mt-2" id="stat-noshow">0</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-3" id="stat-noshow-rate">0% no-show rate</p>
        </div>
    </div>

    <!-- Appointments History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Past Appointments</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1" id="date-range-text">Loading appointments...</p>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Appointment ID
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                            Doctor
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="appointments-table-body" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="animate-spin h-8 w-8 text-sky-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <p>Loading appointments...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-3 sm:px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs sm:text-sm text-gray-600" id="pagination-info">Showing 0 to 0 of 0 appointments</p>
            <div id="pagination-buttons" class="flex flex-wrap gap-2 justify-center">
                <!-- Pagination buttons will be inserted here -->
            </div>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-xl font-bold text-gray-800">Appointment Details</h3>
                <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="appointment-details" class="p-6">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadHistory(1);

            // Add event listener for Enter key on search input
            document.getElementById('search_input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loadHistory(1);
                }
            });
        });

        // Load appointment history
        function loadHistory(page = 1) {
            currentPage = page;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const status = document.getElementById('status_filter').value;
            const search = document.getElementById('search_input').value;

            // Validate: if one date is provided, both must be provided
            if ((fromDate && !toDate) || (!fromDate && toDate)) {
                showError('Please select both From Date and To Date to filter by date range.');
                return;
            }

            // Validate: from date should not be after to date
            if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                showError('From Date cannot be after To Date.');
                return;
            }

            // Validate: dates cannot be in the future
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (fromDate && new Date(fromDate) > today) {
                showError('From Date cannot be a future date.');
                return;
            }
            if (toDate && new Date(toDate) > today) {
                showError('To Date cannot be a future date.');
                return;
            }

            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                status: status,
                search: search,
                page: page
            });

            fetch(`/frontdesk/history?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        updateStatistics(data.statistics);
                        displayAppointments(data.appointments);
                        updatePagination(data.pagination);
                        updateDateRangeText(fromDate, toDate);
                    } else {
                        showError('Failed to load appointments: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Failed to load appointments: ' + error.message);
                });
        }

        // Update statistics cards
        function updateStatistics(stats) {
            document.getElementById('stat-total').textContent = stats.total;
            document.getElementById('stat-completed').textContent = stats.completed;
            document.getElementById('stat-completed-rate').textContent = `${stats.completed_percentage}% success rate`;
            document.getElementById('stat-cancelled').textContent = stats.cancelled;
            document.getElementById('stat-cancelled-rate').textContent = `${stats.cancelled_percentage}% cancellation`;
            document.getElementById('stat-noshow').textContent = stats.no_show;
            document.getElementById('stat-noshow-rate').textContent = `${stats.no_show_percentage}% no-show rate`;
        }

        // Display appointments in table
        function displayAppointments(appointments) {
            const tbody = document.getElementById('appointments-table-body');

            if (!appointments || appointments.length === 0) {
                tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    No appointments found
                </td>
            </tr>
        `;
                return;
            }

            tbody.innerHTML = appointments.map(item => {
                const appointment = item.appointment ?? {};

                const patient = appointment.patient ?? {};
                const doctor = appointment.doctor ?? {};
                const specialty =
                    doctor.doctor_profile?.specialty?.name ?? 'N/A';

                const statusColors = {
                    pending: 'bg-gray-100 text-gray-700',
                    confirmed: 'bg-blue-100 text-blue-700',
                    checked_in: 'bg-teal-100 text-teal-700',
                    in_progress: 'bg-yellow-100 text-yellow-700',
                    completed: 'bg-green-100 text-green-700',
                    cancelled: 'bg-red-100 text-red-700',
                    no_show: 'bg-orange-100 text-orange-700'
                };

                const statusLabels = {
                    pending: 'Pending',
                    confirmed: 'Confirmed',
                    checked_in: 'Checked In',
                    in_progress: 'In Progress',
                    completed: 'Completed',
                    cancelled: 'Cancelled',
                    no_show: 'No-Show'
                };

                const patientName = patient.first_name ?
                    `${patient.first_name} ${patient.last_name}` :
                    'N/A';

                const doctorName = doctor.first_name ?
                    `Dr. ${doctor.first_name} ${doctor.last_name}` :
                    'N/A';

                return `
        <tr class="hover:bg-gray-50">
            <td class="px-3 sm:px-6 py-4 text-sm font-medium text-gray-900">
                ${appointment.appointment_number ?? 'N/A'}
            </td>

            <td class="px-3 sm:px-6 py-4">
                <p class="text-sm text-gray-900">
                    ${formatDate(item.appointment_date)}
                </p>
                <p class="text-sm text-gray-500">
                    ${formatTime(item.appointment_time)}
                </p>
            </td>

            <td class="px-3 sm:px-6 py-4">
                <p class="text-sm font-medium text-gray-900">
                    ${patientName}
                </p>
                <p class="text-xs text-gray-500">
                    ${patient.email ?? 'N/A'}
                </p>
            </td>

            <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                <p class="text-sm text-gray-900">${doctorName}</p>
                <p class="text-xs text-gray-500">${specialty}</p>
            </td>

            <td class="px-3 sm:px-6 py-4">
                <span class="px-3 py-1 text-xs font-medium rounded-full
                    ${statusColors[item.status] ?? ''}">
                    ${statusLabels[item.status] ?? 'N/A'}
                </span>
            </td>

            <td class="px-3 sm:px-6 py-4">
                <button
                    onclick="viewAppointment(${item.id})"
                    class="text-sky-600 hover:text-sky-800 text-sm">
                    View
                </button>
            </td>
        </tr>
        `;
            }).join('');
        }

        // Update pagination
        function updatePagination(pagination) {
            const info = document.getElementById('pagination-info');
            info.textContent =
                `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} appointments`;

            const buttons = document.getElementById('pagination-buttons');
            let html = '';

            // Previous button
            if (pagination.current_page > 1) {
                html +=
                    `<button onclick="loadHistory(${pagination.current_page - 1})" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-xs sm:text-sm">Previous</button>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 1 && i <= pagination
                        .current_page + 1)) {
                    const activeClass = i === pagination.current_page ? 'bg-sky-600 text-white' :
                        'border border-gray-300 text-gray-700 hover:bg-gray-50';
                    html +=
                        `<button onclick="loadHistory(${i})" class="px-3 sm:px-4 py-2 ${activeClass} rounded-lg text-xs sm:text-sm">${i}</button>`;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    html += `<span class="px-2 py-2 text-gray-500">...</span>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                html +=
                    `<button onclick="loadHistory(${pagination.current_page + 1})" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-xs sm:text-sm">Next</button>`;
            }

            buttons.innerHTML = html;
        }

        // View appointment details
        function viewAppointment(id) {
            fetch(`/frontdesk/history/${id}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAppointmentDetails(data.appointment);
                        document.getElementById('viewModal').classList.remove('hidden');
                        document.getElementById('viewModal').classList.add('flex');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Failed to load appointment details');
                });
        }

        // Display appointment details in modal
        function displayAppointmentDetails(appointment) {
            const statusColors = {
                pending: 'bg-gray-100 text-gray-700',
                confirmed: 'bg-blue-100 text-blue-700',
                checked_in: 'bg-teal-100 text-teal-700',
                in_progress: 'bg-yellow-100 text-yellow-700',
                completed: 'bg-green-100 text-green-700',
                cancelled: 'bg-red-100 text-red-700',
                no_show: 'bg-orange-100 text-orange-700'
            };

            const statusLabels = {
                pending: 'Pending',
                confirmed: 'Confirmed',
                checked_in: 'Checked In',
                in_progress: 'In Progress',
                completed: 'Completed',
                cancelled: 'Cancelled',
                no_show: 'No-Show'
            };

            let html = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Appointment Number</label>
                        <p class="mt-1 text-base font-semibold text-gray-900">${appointment.appointment_number}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 ${statusColors[appointment.status]} text-sm font-medium rounded-full">
                                ${statusLabels[appointment.status]}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Patient Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.patient_name}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.patient_email}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.patient_phone}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Doctor Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.doctor_name}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Specialization</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.specialization}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Appointment Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.appointment_date}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Time</label>
                            <p class="mt-1 text-base text-gray-900">${appointment.appointment_time}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type</label>
                            <p class="mt-1 text-base text-gray-900">${capitalizeFirst(appointment.appointment_type)}</p>
                        </div>
                    </div>
                </div>

                ${appointment.reason_for_visit ? `
                            <div class="border-t pt-4">
                                <label class="text-sm font-medium text-gray-500">Reason for Visit</label>
                                <p class="mt-1 text-base text-gray-900">${appointment.reason_for_visit}</p>
                            </div>
                        ` : ''}

                ${appointment.symptoms ? `
                            <div class="border-t pt-4">
                                <label class="text-sm font-medium text-gray-500">Symptoms</label>
                                <p class="mt-1 text-base text-gray-900">${appointment.symptoms}</p>
                            </div>
                        ` : ''}

                ${appointment.notes ? `
                            <div class="border-t pt-4">
                                <label class="text-sm font-medium text-gray-500">Notes</label>
                                <p class="mt-1 text-base text-gray-900">${appointment.notes}</p>
                            </div>
                        ` : ''}

                ${appointment.cancellation_reason ? `
                            <div class="border-t pt-4">
                                <label class="text-sm font-medium text-gray-500">Cancellation Reason</label>
                                <p class="mt-1 text-base text-red-600">${appointment.cancellation_reason}</p>
                            </div>
                        ` : ''}
            </div>
            `;

            document.getElementById('appointment-details').innerHTML = html;
        }

        // Close view modal
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            document.getElementById('viewModal').classList.remove('flex');
        }

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function formatTime(timeString) {
            const time = new Date(`2000-01-01 ${timeString}`);
            return time.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getInitials(firstName, lastName) {
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).replace('_', ' ');
        }

        function updateDateRangeText(fromDate, toDate) {
            const text = document.getElementById('date-range-text');
            if (fromDate && toDate) {
                text.textContent = `Showing appointments from ${formatDate(fromDate)} to ${formatDate(toDate)}`;
            } else {
                text.textContent = 'Showing all past appointments';
            }
        }

        function showError(message) {
            alert(message);
        }

        function exportReport() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const status = document.getElementById('status_filter').value;
            const search = document.getElementById('search_input').value;

            // Validate: if one date is provided, both must be provided
            if ((fromDate && !toDate) || (!fromDate && toDate)) {
                showError('Please select both From Date and To Date to export by date range.');
                return;
            }

            // Validate: from date should not be after to date
            if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                showError('From Date cannot be after To Date.');
                return;
            }

            // Validate: dates cannot be in the future
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (fromDate && new Date(fromDate) > today) {
                showError('From Date cannot be a future date.');
                return;
            }
            if (toDate && new Date(toDate) > today) {
                showError('To Date cannot be a future date.');
                return;
            }

            const params = new URLSearchParams();
            if (fromDate) params.append('from_date', fromDate);
            if (toDate) params.append('to_date', toDate);
            if (status && status !== 'all') params.append('status', status);
            if (search) params.append('search', search);

            // Open download in new window/tab
            window.location.href = `/frontdesk/history/export/csv?${params.toString()}`;
        }
    </script>
@endsection
