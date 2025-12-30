@extends('layouts.admin')

@section('title', 'Appointments Management')
@section('page-title', 'Appointments Management')

@section('header-actions')
    <a href="{{ route('admin.add-appointment') }}"
        class="px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium"
        {{ request()->routeIs('admin.appointments*') ? 'text-white bg-sky-600' : 'text-gray-700 hover:bg-gray-100' }}>+
        Add Appointment</a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="filterSearch" placeholder="Patient name, ID..."
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Doctor</label>
                <select id="filterDoctor"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}">Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" id="filterDate"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="filterStatus"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No Show</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="applyFiltersBtn"
                    class="w-full px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">Apply
                    Filters</button>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Appointment ID</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Patient</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                            Doctor</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date & Time</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                            Type</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTableBody" class="divide-y divide-gray-200">
                    <!-- Dynamic Rows will load here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
            <div id="paginationInfo" class="text-xs sm:text-sm text-gray-600"></div>
            <div id="paginationContainer" class="flex flex-wrap gap-2 justify-center"></div>
        </div>

        <!-- Custom Delete Modal -->
        <div id="customDeleteModal"
            class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-80">
                <h2 class="text-lg font-semibold mb-4">Confirm Delete</h2>
                <p id="deleteModalText" class="text-gray-700 mb-6">Are you sure you want to delete this appointments?</p>

                <div class="flex justify-end space-x-3">
                    <button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let deleteId = null; // make these global within this script
            let deleteName = null;

            document.addEventListener("DOMContentLoaded", function() {
                loadAppointments(1); // load default page

                // When delete button is clicked → OPEN MODAL
                $(document).on('click', '.delete-appointment-btn', function() {
                    deleteId = $(this).data('id');
                    deleteName = $(this).data('appointment-no');

                    document.getElementById('deleteModalText').textContent =
                        `Are you sure you want to delete the appointment "${deleteName}"?`;

                    document.getElementById('customDeleteModal').classList.remove('hidden');
                });

                // Cancel button → CLOSE MODAL
                document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
                    document.getElementById('customDeleteModal').classList.add('hidden');
                    deleteId = null;
                    deleteName = null;
                });

                // Confirm delete → DELETE appointment
                document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                    if (!deleteId) return;

                    $.ajax({
                        url: "{{ route('admin.delete-appointment') }}",
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            appointment_id: deleteId
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.msg);
                                setTimeout(function() {
                                    loadAppointments(1);
                                }, 500);
                            } else {
                                toastr.error(response.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            try {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    Object.keys(errors).forEach(function(key) {
                                        toastr.error(errors[key][0]);
                                    });
                                } else {
                                    toastr.error("An error occurred: " + error);
                                }
                            } catch (e) {
                                toastr.error("A server error occurred.");
                                console.error(e);
                            }
                        }
                    });

                    document.getElementById('customDeleteModal').classList.add('hidden');
                    deleteId = null;
                    deleteName = null;
                });
            });

            // Apply Filters Button Click
            $('#applyFiltersBtn').on('click', function() {
                loadAppointments(1); // reload first page with filters
            });

            function getFilters() {
                return {
                    search: $('#filterSearch').val(),
                    doctor_id: $('#filterDoctor').val(), // must match backend
                    date: $('#filterDate').val(),
                    status: $('#filterStatus').val() // backend already expects 'status'
                };
            }

            function loadAppointments(page = 1) {
                let filters = getFilters();

                let query = `?page=${page}`;
                if (filters.search) query += `&search=${filters.search}`;
                if (filters.doctor_id) query += `&doctor_id=${filters.doctor_id}`; // fix here
                if (filters.date) query += `&date=${filters.date}`;
                if (filters.status) query += `&status=${filters.status}`;

                fetch("{{ route('admin.appointments-list') }}" + query).then(response => response.json()).then(res => {
                    let data = res.data;
                    let tbody = document.getElementById("appointmentsTableBody");
                    tbody.innerHTML = "";

                    if (data.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">No Appointments Found</td>
                            </tr>`;
                        document.querySelector("#paginationContainer").innerHTML = '';
                        document.querySelector("#paginationInfo").innerHTML = '';
                        return;
                    }

                    data.data.forEach(app => {
                        let patientFirstName = app.patient && app.patient.first_name ? app.patient.first_name :
                            '';
                        let patientLastName = app.patient && app.patient.last_name ? app.patient.last_name : '';
                        let patientPhone = app.patient && app.patient.phone ? app.patient.phone : '';

                        let doctorFirstName = app.doctor && app.doctor.first_name ? app.doctor.first_name : '';
                        let doctorLastName = app.doctor && app.doctor.last_name ? app.doctor.last_name : '';
                        let doctorSpecialty = app.doctor && app.doctor.doctor_profile && app.doctor
                            .doctor_profile.specialty ? app.doctor.doctor_profile.specialty.name : 'N/A';

                        let row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3"><span class="text-sm font-medium text-sky-600">${app.appointment_number}</span></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center text-sky-600 font-semibold z-10">
                                            ${patientLastName.substring(0, 2).toUpperCase()}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-800">${patientFirstName} ${patientLastName}</p>
                                            <p class="text-xs text-gray-500">${patientPhone}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <p class="text-sm font-medium text-gray-800">${doctorFirstName} ${doctorLastName}</p>
                                    <p class="text-xs text-gray-500">${doctorSpecialty}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-800">${app.formatted_date} , ${app.formatted_time}</p>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <span class="px-3 py-1 text-xs font-medium ${getTypeColor(app.appointment_type)} rounded-full">
                                        ${formatLabel(app.appointment_type)}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 text-xs font-medium ${getStatusColor(app.status)} rounded-full">
                                        ${formatLabel(app.status)}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex space-x-1 sm:space-x-2">
                                        <button class="text-sky-600 hover:text-sky-800 view-appointment-btn" data-id="${app.id}">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button class="text-amber-600 hover:text-amber-800 edit-appointment-btn" 
                                            href="javascript:;" 
                                            data-id="${app.id}">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 delete-appointment-btn" data-id="${app.id}" data-appointment-no="${app.appointment_number }">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                        tbody.innerHTML += row;
                    });
                    updatePagination(data);
                });
            }

            // Helper Functions
            function formatLabel(text) {
                if (!text) return "";
                return text.replace(/_/g, " ").toLowerCase().replace(/\b\w/g, (c) => c.toUpperCase());
            }

            function getStatusColor(status) {
                status = status.toLowerCase();
                switch (status) {
                    case 'confirmed':
                        return 'bg-green-100 text-green-700';
                    case 'pending':
                        return 'bg-yellow-100 text-yellow-700';
                    case 'checked_in':
                        return 'bg-indigo-100 text-indigo-700';
                    case 'in_progress':
                        return 'bg-blue-100 text-blue-700';
                    case 'completed':
                        return 'bg-emerald-100 text-emerald-700';
                    case 'cancelled':
                        return 'bg-red-100 text-red-700';
                    case 'no_show':
                        return 'bg-gray-200 text-gray-700';
                    default:
                        return 'bg-gray-100 text-gray-700';
                }
            }

            function getTypeColor(type) {
                type = type.toLowerCase();
                switch (type) {
                    case 'consultation':
                        return 'bg-blue-100 text-blue-700';
                    case 'follow_up':
                        return 'bg-purple-100 text-purple-700';
                    case 'emergency':
                        return 'bg-red-100 text-red-700';
                    case 'check_up':
                        return 'bg-blue-100 text-blue-700';
                    default:
                        return 'bg-gray-100 text-gray-700';
                }
            }

            function updatePagination(data) {
                let paginationHTML = "";

                paginationHTML += `
                <button onclick="loadAppointments(${data.prev_page_url ? data.current_page - 1 : data.current_page})"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${data.prev_page_url ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' : 'text-gray-400 bg-gray-100 cursor-not-allowed'} rounded-lg">
                    Previous
                </button>
            `;

                for (let i = 1; i <= data.last_page; i++) {
                    paginationHTML += `
                    <button onclick="loadAppointments(${i})"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${i === data.current_page ? 'text-white bg-sky-600' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'} rounded-lg">
                        ${i}
                    </button>`;
                }

                paginationHTML += `
                <button onclick="loadAppointments(${data.next_page_url ? data.current_page + 1 : data.current_page})"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${data.next_page_url ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' : 'text-gray-400 bg-gray-100 cursor-not-allowed'} rounded-lg">
                    Next
                </button>
            `;

                document.querySelector("#paginationContainer").innerHTML = paginationHTML;
                document.querySelector("#paginationInfo").innerHTML =
                    `Showing <span class="font-medium">${data.from}</span> to <span class="font-medium">${data.to}</span> of <span class="font-medium">${data.total}</span> results`;
            }
        </script>
        <script>
            // Handling the click event for the View button
            $('body').on('click', '.view-appointment-btn', function() {
                var appointmentId = $(this).data('id');
                $.ajax({
                    url: "{{ route('admin.view-appointment', ['id' => 'ID_PLACEHOLDER']) }}".replace(
                        'ID_PLACEHOLDER', appointmentId),
                    type: 'GET',
                    success: function(response) {
                        if (response.data) {
                            // Load view template
                            loadViewAppointmentTemplate(response.data);
                        }
                    },
                    error: function(error) {
                        console.error("Error: ", error);
                        toastr.error('Failed to load appointment details');
                    }
                });
            });

            // Handling the click event for the Edit button
            $('body').on('click', '.edit-appointment-btn', function() {
                var appointmentId = $(this).data('id');
                $.ajax({
                    url: "{{ route('admin.getappointment-modal') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        appointment_id: appointmentId
                    },
                    success: function(data) {
                        $('.addmodalbody').html(data);
                        $('.add_modal').removeClass('hidden');

                        // Initialize Select2 for the edit form
                        initializeEditFormSelects();

                        // Handle form submission
                        $('#editAppointmentForm').on('submit', function(e) {
                            e.preventDefault();
                            submitEditAppointmentForm();
                        });
                    },
                    error: function(error) {
                        console.error("Error: ", error);
                        toastr.error('Failed to load appointment details');
                    }
                });
            });


            function loadViewAppointmentTemplate(appointment) {
                var html = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Appointment ID</label>
                                <p class="text-sm sm:text-base font-semibold text-gray-800">${appointment.appointment_number}</p>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Status</label>
                                <p class="text-sm sm:text-base">
                                    <span class="px-3 py-1 text-xs font-medium ${getStatusColor(appointment.status)} rounded-full">
                                        ${formatLabel(appointment.status)}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Patient Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Patient Name</label>
                                    <p class="text-sm sm:text-base text-gray-800">${appointment.patient.first_name} ${appointment.patient.last_name}</p>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Phone</label>
                                    <p class="text-sm sm:text-base text-gray-800">${appointment.patient.phone || 'N/A'}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Doctor Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Doctor Name</label>
                                    <p class="text-sm sm:text-base text-gray-800">Dr. ${appointment.doctor.first_name} ${appointment.doctor.last_name}</p>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Specialty</label>
                                    <p class="text-sm sm:text-base text-gray-800">${appointment.doctor.doctor_profile.specialty.name}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Appointment Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Date</label>
                                    <p class="text-sm sm:text-base text-gray-800">${appointment.formatted_date}</p>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Time</label>
                                    <p class="text-sm sm:text-base text-gray-800">${appointment.formatted_time}</p>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Type</label>
                                    <p class="text-sm sm:text-base">
                                        <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                            ${formatLabel(appointment.appointment_type)}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-2">Reason for Visit</label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700">${appointment.reason_for_visit}</p>
                            </div>
                        </div>

                        ${appointment.status === 'cancelled' && appointment.cancellation_reason ? `
                                    <div class="border-t pt-4">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-2">Cancellation Reason</label>
                                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                            <p class="text-sm text-red-700">${appointment.cancellation_reason}</p>
                                        </div>
                                    </div>
                                ` : ''}

                        ${appointment.notes ? `
                                    <div class="border-t pt-4">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-2">Additional Notes</label>
                                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                            <p class="text-sm text-gray-700">${appointment.notes}</p>
                                        </div>
                                    </div>
                                ` : ''}

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button" onclick="closeEditModal()"
                                class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                Close
                            </button>
                            <button type="button" onclick="editAppointmentFromView(${appointment.id})"
                                class="px-6 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
                                Edit Appointment
                            </button>
                        </div>
                    </div>
                `;

                $('.addmodalbody').html(html);
                $('.add_modal').removeClass('hidden');
            }

            function editAppointmentFromView(appointmentId) {
                $.ajax({
                    url: "{{ route('admin.getappointment-modal') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        appointment_id: appointmentId
                    },
                    success: function(data) {
                        $('.addmodalbody').html(data);

                        // Initialize Select2 for the edit form
                        initializeEditFormSelects();

                        // Handle form submission
                        $('#editAppointmentForm').on('submit', function(e) {
                            e.preventDefault();
                            submitEditAppointmentForm();
                        });
                    },
                    error: function(error) {
                        console.error("Error: ", error);
                        toastr.error('Failed to load appointment details');
                    }
                });
            }

            function initializeEditFormSelects() {
                // Initialize Select2 for edit form fields
                $('#edit_doctor_select').select2({
                    placeholder: 'Search or select doctor...',
                    allowClear: false,
                    width: '100%'
                });

                $('#edit_type_select').select2({
                    placeholder: 'Select type...',
                    allowClear: false,
                    width: '100%'
                });

                $('#edit_select_patient').select2({
                    placeholder: 'Select Patient',
                    allowClear: false,
                    width: '100%'
                });
            }

            function submitEditAppointmentForm() {
                var formData = {
                    appointment_id: $('#appointment_id').val(),
                    patient_id: $('#edit_select_patient').val(),
                    doctor_id: $('#edit_doctor_select').val(),
                    appointment_date: $('#edit_appointment_date').val(),
                    appointment_time: $('#edit_appointment_time').val(),
                    appointment_type: $('#edit_type_select').val(),
                    reason_for_visit: $('#edit_reason_for_visit').val(),
                    notes: $('#edit_notes').val(),
                    status: $('#edit_status').val(),
                    cancellation_reason: $('#edit_cancellation_reason').val()
                };

                const submitBtn = $('#editAppointmentForm').find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.update-appointment') }}",
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    success: function(response) {
                        try {
                            if (response.status == 200) {
                                toastr.success(response.msg);
                                closeEditModal();
                                setTimeout(function() {
                                    loadAppointments(1);
                                }, 500);
                            } else {
                                toastr.error(response.msg);
                                submitBtn.prop('disabled', false).html(originalText);
                            }
                        } catch (e) {
                            toastr.error("An error occurred while processing the response.");
                            console.error(e);
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        try {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function(key) {
                                    toastr.error(errors[key][0]);
                                });
                            } else {
                                toastr.error("An error occurred: " + error);
                            }
                        } catch (e) {
                            toastr.error("A server error occurred.");
                            console.error(e);
                        }
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            }

            function closeEditModal() {
                $('.add_modal').addClass('hidden');
                $('.addmodalbody').html('');
            }
        </script>
    @endpush
@endsection
