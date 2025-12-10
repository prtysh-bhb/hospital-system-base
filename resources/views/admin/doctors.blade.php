@extends('layouts.admin')

@section('title', 'Doctors Management')

@section('page-title', 'Doctors Management')

@section('header-actions')
    <a href="{{ route('admin.doctors.add') }}"
        class="px-4 sm:px-6 py-2 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">+ Add
        Doctor</a>
@endsection

@section('content')
    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Doctor View Modal -->
    <div id="doctorViewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">Doctor Details</h2>
                <button onclick="closeDoctorViewModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- Loading State -->
                <div id="doctorViewLoading" class="p-8 text-center">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
                    <p class="mt-3 text-gray-600">Loading doctor details...</p>
                </div>

                <!-- Doctor Details Content -->
                <div id="doctorViewContent" class="hidden">
                    <!-- Profile Section -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                            <img id="doctorImage"
                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-white shadow-lg object-cover"
                                alt="Doctor Profile"
                                onerror="this.onerror=null; this.src=`https://ui-avatars.com/api/?name=${encodeURIComponent(this.dataset.name || 'Doctor')}&background=0ea5e9&color=fff&size=128`;">
                            <div class="text-center sm:text-left flex-1">
                                <h3 id="doctorName" class="text-2xl font-bold text-gray-800"></h3>
                                <p id="doctorSpecialty" class="text-sky-600 font-semibold mt-1"></p>
                                <p id="doctorQualification" class="text-gray-600 text-sm mt-1"></p>
                                <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                                    <span id="doctorStatus" class="px-3 py-1 text-xs font-medium rounded-full"></span>
                                    <span id="doctorAvailability" class="px-3 py-1 text-xs font-medium rounded-full"></span>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-3 gap-4 p-6 bg-gray-50">
                        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                            <p id="totalAppointments" class="text-2xl font-bold text-sky-600">0</p>
                            <p class="text-xs text-gray-500 mt-1">Total Appointments</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                            <p id="completedAppointments" class="text-2xl font-bold text-green-600">0</p>
                            <p class="text-xs text-gray-500 mt-1">Completed</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                            <p id="upcomingAppointments" class="text-2xl font-bold text-amber-600">0</p>
                            <p class="text-xs text-gray-500 mt-1">Upcoming</p>
                        </div>
                    </div>

                    <!-- Personal & Professional Info -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-sky-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Personal Information
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Email:</span>
                                    <span id="doctorEmail" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Phone:</span>
                                    <span id="doctorPhone" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Gender:</span>
                                    <span id="doctorGender" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Date of Birth:</span>
                                    <span id="doctorDob" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Address:</span>
                                    <span id="doctorAddress" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-sky-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Professional Information
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Experience:</span>
                                    <span id="doctorExperience" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">License No:</span>
                                    <span id="doctorLicense" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Consultation:</span>
                                    <span id="doctorFee" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-500 text-sm w-28 flex-shrink-0">Joined:</span>
                                    <span id="doctorJoined" class="text-gray-800 text-sm font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Section -->
                    <div class="p-6 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sky-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Weekly Schedule
                        </h4>
                        <div id="doctorSchedules" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <!-- Schedules will be populated here -->
                        </div>
                        <div id="noSchedules" class="hidden text-center py-4 text-gray-500 text-sm">
                            No schedules configured for this doctor.
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="p-6 border-t border-gray-100 bg-gray-50">
                        <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sky-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            About
                        </h4>
                        <p id="doctorBio" class="text-gray-600 text-sm leading-relaxed"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button onclick="closeDoctorViewModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <a id="editDoctorBtn" href="#"
                    class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors">
                    Edit Doctor
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="md:col-span-2">
                <input type="text" id="searchInput" placeholder="Search by name, specialty, email, phone..."
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            <div>
                <select id="specialtyFilter"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Specialties</option>
                    @foreach ($specialties as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="statusFilter"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
        <p class="mt-2 text-sm text-gray-600">Loading doctors...</p>
    </div>

    <!-- Doctors Grid -->
    <div id="doctorsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @include('admin.partials.doctor-cards', ['doctors' => $doctors])
    </div>

    <!-- Custom Delete Modal -->
    <div id="customDeleteModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h2 class="text-lg font-semibold mb-4">Confirm Delete</h2>
            <p id="deleteModalText" class="text-gray-700 mb-6">Are you sure you want to delete this doctor?</p>

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

    <script>
        let searchTimeout;

        document.addEventListener("DOMContentLoaded", function() {
            $(document).on('click', '.delete-doctor-btn', function() {
                deleteDoctorId = $(this).data('doctor-id');
                let doctorName = $(this).data('doctor-name');

                document.getElementById('deleteModalText').textContent =
                    `Are you sure you want to delete the doctor "${doctorName}"?`;

                document.getElementById('customDeleteModal').classList.remove('hidden');
            });

            // Cancel button closes modal
            document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
                document.getElementById('customDeleteModal').classList.add('hidden');
                deleteDoctorId = null;
            });
        });

        // Notification function
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';

            notification.className =
                `${bgColor} border px-4 py-3 rounded-lg shadow-lg flex items-center justify-between min-w-[300px]`;
            notification.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            `;
            container.appendChild(notification);
            setTimeout(() => notification.remove(), 5000);
        }

        function fetchDoctors() {
            const search = document.getElementById('searchInput').value;
            const specialty_id = document.getElementById('specialtyFilter').value;
            const status = document.getElementById('statusFilter').value;

            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('doctorsGrid').style.opacity = '0.5';

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (specialty_id) params.append('specialty_id', specialty_id);
            if (status) params.append('status', status);

            fetch(`{{ route('admin.doctors') }}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('doctorsGrid').innerHTML = html;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('doctorsGrid').style.opacity = '1';
                    attachDeleteHandlers();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('doctorsGrid').style.opacity = '1';
                    showNotification('An error occurred while fetching doctors.', 'error');
                });
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteDoctorId) return;
            fetch(`/admin/doctors/${deleteDoctorId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message, 'success');
                        fetchDoctors();
                        document.getElementById('customDeleteModal').classList.add('hidden');
                    } else {
                        toastr.error(data.message || 'Failed to delete doctor', 'error');
                    }
                })
                .catch(error => {
                    toastr.error('An error occurred while deleting the doctor.', 'error');
                });

        });

        function attachDeleteHandlers() {
            // Attach view details handlers
            document.querySelectorAll('.view-doctor-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const doctorId = this.dataset.doctorId;
                    viewDoctorDetails(doctorId);
                });
            });
        }

        // View Doctor Details Modal Functions
        function viewDoctorDetails(doctorId) {
            const modal = document.getElementById('doctorViewModal');
            const loading = document.getElementById('doctorViewLoading');
            const content = document.getElementById('doctorViewContent');

            // Show modal with loading state
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            fetch(`/admin/doctors/${doctorId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateDoctorModal(data);
                        loading.classList.add('hidden');
                        content.classList.remove('hidden');
                    } else {
                        showNotification(data.message || 'Failed to load doctor details', 'error');
                        closeDoctorViewModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while loading doctor details.', 'error');
                    closeDoctorViewModal();
                });
        }

        function populateDoctorModal(data) {
            const doctor = data.doctor;
            const schedules = data.schedules;
            const stats = data.statistics;

            // Profile Image with fallback
            const imgEl = document.getElementById('doctorImage');
            imgEl.dataset.name = doctor.full_name; // Set data-name for onerror fallback

            if (doctor.profile_image) {
                imgEl.src = doctor.profile_image;
            } else {
                imgEl.src =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(doctor.full_name)}&background=0ea5e9&color=fff&size=128`;
            }

            // Basic Info
            document.getElementById('doctorName').textContent = doctor.full_name;
            document.getElementById('doctorSpecialty').textContent = doctor.specialty;
            document.getElementById('doctorQualification').textContent = doctor.qualification;

            // Status Badge
            const statusEl = document.getElementById('doctorStatus');
            if (doctor.status === 'active') {
                statusEl.textContent = 'Active';
                statusEl.className = 'px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700';
            } else {
                statusEl.textContent = 'Inactive';
                statusEl.className = 'px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700';
            }

            // Availability Badge
            const availEl = document.getElementById('doctorAvailability');
            if (doctor.available_for_booking) {
                availEl.textContent = 'Available for Booking';
                availEl.className = 'px-3 py-1 text-xs font-medium rounded-full bg-sky-100 text-sky-700';
            } else {
                availEl.textContent = 'Not Available';
                availEl.className = 'px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700';
            }

            // Statistics
            document.getElementById('totalAppointments').textContent = stats.total_appointments;
            document.getElementById('completedAppointments').textContent = stats.completed_appointments;
            document.getElementById('upcomingAppointments').textContent = stats.upcoming_appointments;

            // Personal Info
            document.getElementById('doctorEmail').textContent = doctor.email;
            document.getElementById('doctorPhone').textContent = doctor.phone || 'N/A';
            document.getElementById('doctorGender').textContent = doctor.gender;
            document.getElementById('doctorDob').textContent = doctor.date_of_birth;
            document.getElementById('doctorAddress').textContent = doctor.address;

            // Professional Info
            document.getElementById('doctorExperience').textContent = doctor.experience_years + ' years';
            document.getElementById('doctorLicense').textContent = doctor.license_number;
            document.getElementById('doctorFee').textContent = '₹' + doctor.consultation_fee;
            document.getElementById('doctorJoined').textContent = doctor.created_at;

            // Bio
            document.getElementById('doctorBio').textContent = doctor.bio;

            // Schedules
            const schedulesContainer = document.getElementById('doctorSchedules');
            const noSchedules = document.getElementById('noSchedules');
            schedulesContainer.innerHTML = '';

            if (schedules && schedules.length > 0) {
                noSchedules.classList.add('hidden');
                schedules.forEach(schedule => {
                    const scheduleCard = document.createElement('div');
                    scheduleCard.className = 'bg-sky-50 rounded-lg p-3 border border-sky-100';
                    scheduleCard.innerHTML = `
                        <div class="font-semibold text-sky-700 text-sm">${schedule.day}</div>
                        <div class="text-gray-600 text-xs mt-1">${schedule.start_time} - ${schedule.end_time}</div>
                        <div class="text-gray-500 text-xs">${schedule.slot_duration} min slots</div>
                    `;
                    schedulesContainer.appendChild(scheduleCard);
                });
            } else {
                noSchedules.classList.remove('hidden');
            }

            // Edit Button
            document.getElementById('editDoctorBtn').href = `/admin/doctors/${doctor.id}/edit`;
        }

        function closeDoctorViewModal() {
            const modal = document.getElementById('doctorViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDoctorViewModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('doctorViewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDoctorViewModal();
            }
        });

        // Initial attachment
        document.addEventListener('DOMContentLoaded', attachDeleteHandlers);

        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchDoctors(), 500);
        });

        document.getElementById('specialtyFilter').addEventListener('change', fetchDoctors);
        document.getElementById('statusFilter').addEventListener('change', fetchDoctors);
    </script>
@endsection
