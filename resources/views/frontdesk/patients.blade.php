@extends('layouts.frontdesk')

@section('title', 'Patients')

@section('page-title', 'Patients')

@section('content')
    <!-- Search & Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="col-span-1 md:col-span-2">
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search Patient</label>
                <input type="text" id="searchInput" placeholder="Search by name, email, phone, or ID..."
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Gender</label>
                <select id="genderFilter"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                    <option value="all">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="searchBtn"
                    class="w-full px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">All Patients</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">View and manage patient records</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xl sm:text-2xl font-bold text-gray-800" id="totalPatients">0</p>
                <p class="text-xs sm:text-sm text-gray-500">Total Patients</p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient ID
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient Name
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                            Age/Gender
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            Last Visit
                        </th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="patientsTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Loading patients...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-3 sm:px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs sm:text-sm text-gray-600" id="paginationInfo">Loading...</p>
            <div id="paginationButtons" class="flex flex-wrap gap-2 justify-center">
                <!-- Pagination buttons will be loaded here -->
            </div>
        </div>
    </div>

    <!-- View Patient Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Patient Details</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="viewModalContent" class="p-6">
                <!-- Patient details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Edit Patient</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="editPatientForm" class="p-6">
                @csrf
                <input type="hidden" id="edit_patient_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" id="edit_first_name" data-error="edit_first_name_error"
                            placeholder="Enter first name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_first_name_error"></span>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" id="edit_last_name" data-error="edit_last_name_error"
                            placeholder="Enter last name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_last_name_error"></span>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-red-600">*</span></label>
                        <input type="email" id="edit_email" data-error="edit_email_error" placeholder="Enter email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_email_error"></span>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone <span
                                class="text-red-600">*</span></label>
                        <input type="tel" id="edit_phone" data-error="edit_phone_error"
                            placeholder="Enter phone number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_phone_error"></span>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth <span
                                class="text-red-600">*</span></label>
                        <input type="date" id="edit_date_of_birth" data-error="edit_date_of_birth_error"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_date_of_birth_error"></span>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span
                                class="text-red-600">*</span></label>
                        <select id="edit_gender" data-error="edit_gender_error"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="text-xs text-red-500 hidden" id="edit_gender_error"></span>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea id="edit_address" data-error="edit_address_error" placeholder="Enter address..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"></textarea>
                        <span class="text-xs text-red-500 hidden" id="edit_address_error"></span>
                    </div>

                    <!-- Blood Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                        <input type="text" id="edit_blood_group" data-error="edit_blood_group_error"
                            placeholder="Enter blood group"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_blood_group_error"></span>
                    </div>

                    <!-- Emergency Contact Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                        <input type="text" id="edit_emergency_contact_name"
                            data-error="edit_emergency_contact_name_error" placeholder="Enter emergency contact name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_emergency_contact_name_error"></span>
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                        <input type="tel" id="edit_emergency_contact_phone"
                            data-error="edit_emergency_contact_phone_error" placeholder="Enter emergency phone number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-red-500 hidden" id="edit_emergency_contact_phone_error"></span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="savePatientBtn"
                        class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePatientModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-96 p-6">
            <h3 class="text-lg font-semibold mb-4">Confirm Deletion</h3>
            <p class="mb-6">Are you sure you want to delete this patient? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button id="cancelDeleteBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let currentPatient = null;
        let patientToDeleteId = null;

        function deletePatient(id) {
            patientToDeleteId = id;
            document.getElementById('deletePatientModal').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadPatients();

            // Search functionality
            document.getElementById('searchBtn').addEventListener('click', () => {
                currentPage = 1;
                loadPatients();
            });

            document.getElementById('searchInput').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    currentPage = 1;
                    loadPatients();
                }
            });

            document.getElementById('genderFilter').addEventListener('change', () => {
                currentPage = 1;
                loadPatients();
            });

            // Form validation
            document.getElementById('edit_first_name').addEventListener('input', function() {
                this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            });

            document.getElementById('edit_last_name').addEventListener('input', function() {
                this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            });

            document.getElementById('edit_phone').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Edit form submission
            document.getElementById('editPatientForm').addEventListener('submit', handleEditSubmit);
        });

        document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
            patientToDeleteId = null;
            document.getElementById('deletePatientModal').classList.add('hidden');
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            if (!patientToDeleteId) return;

            const id = patientToDeleteId;
            document.getElementById('deletePatientModal').classList.add('hidden');
            patientToDeleteId = null;

            fetch(`{{ url('frontdesk/patients') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        loadPatients(currentPage);
                    } else {
                        toastr.error(data.message || 'Failed to delete patient');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while deleting the patient');
                });
        });

        function loadPatients(page = 1) {
            const search = document.getElementById('searchInput').value;
            const gender = document.getElementById('genderFilter').value;
            currentPage = page;

            fetch(`{{ route('frontdesk.patients') }}?search=${encodeURIComponent(search)}&gender=${gender}&page=${page}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPatients(data.patients);
                        updatePagination(data.pagination);
                        document.getElementById('totalPatients').textContent = data.pagination.total;
                    }
                })
                .catch(error => {
                    console.error('Error loading patients:', error);
                });
        }

        function displayPatients(patients) {
            const tbody = document.getElementById('patientsTableBody');

            if (patients.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No patients found
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = patients.map(patient => {
                const age = calculateAge(patient.date_of_birth);
                const lastVisit = patient.patient_appointments && patient.patient_appointments.length > 0 ? patient
                    .patient_appointments[
                        0] : null;

                return `
                <tr class="hover:bg-gray-50">
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        <p class="text-xs sm:text-sm font-medium text-gray-900">PT-${String(patient.id).padStart(4, '0')}</p>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(patient.first_name + ' ' + patient.last_name)}&background=0ea5e9&color=fff"
                                 class="w-8 h-8 sm:w-10 sm:h-10 rounded-full" alt="Patient">
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-900">${patient.first_name} ${patient.last_name}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                        <p class="text-xs sm:text-sm text-gray-900">${age} • ${capitalizeFirst(patient.gender)}</p>
                    </td>
                    <td class="px-3 sm:px-6 py-4">
                        <p class="text-xs sm:text-sm text-gray-900 break-all">${patient.email || 'N/A'}</p>
                        <p class="text-xs sm:text-sm text-gray-500">${patient.phone || 'N/A'}</p>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                        ${lastVisit ? `
                                                                                                                                                                                <p class="text-xs sm:text-sm text-gray-900">${formatDate(lastVisit.appointment_date)}</p>
                                                                                                                                                                                <p class="text-xs sm:text-sm text-gray-500">${lastVisit.doctor?.first_name} ${lastVisit.doctor?.last_name}</p>
                                                                                                                                                                            ` : '<p class="text-xs sm:text-sm text-gray-500">No visits yet</p>'}
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                        <div class="flex gap-2">
                            <button onclick="viewPatient(${patient.id})" 
                                    class="px-2 sm:px-3 py-1 bg-sky-100 text-sky-700 rounded hover:bg-sky-200 text-xs sm:text-sm">
                                View
                            </button>
                            <button onclick="deletePatient(${patient.id})" 
                                    class="px-2 sm:px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs sm:text-sm">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        }

        function updatePagination(pagination) {
            const info = document.getElementById('paginationInfo');
            info.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} patients`;

            const buttons = document.getElementById('paginationButtons');
            let html = '';

            // Previous button
            html += `
            <button onclick="loadPatients(${pagination.current_page - 1})" 
                    ${pagination.current_page === 1 ? 'disabled' : ''}
                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-xs sm:text-sm ${pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                Previous
            </button>
        `;

            // Page numbers
            for (let i = 1; i <= Math.min(pagination.last_page, 5); i++) {
                if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 1 && i <= pagination
                        .current_page + 1)) {
                    html += `
                    <button onclick="loadPatients(${i})"
                            class="px-3 sm:px-4 py-2 ${i === pagination.current_page ? 'bg-sky-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'} rounded-lg text-xs sm:text-sm">
                        ${i}
                    </button>
                `;
                }
            }

            // Next button
            html += `
            <button onclick="loadPatients(${pagination.current_page + 1})"
                    ${pagination.current_page === pagination.last_page ? 'disabled' : ''}
                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-xs sm:text-sm ${pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : ''}">
                Next
            </button>
        `;

            buttons.innerHTML = html;
        }

        function viewPatient(id) {
            fetch(`{{ url('frontdesk/patients') }}/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentPatient = data.patient;
                        displayPatientDetails(data.patient);
                        document.getElementById('viewModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading patient:', error);
                    alert('Failed to load patient details');
                });
        }

        function displayPatientDetails(patient) {
            const age = calculateAge(patient.date_of_birth);
            const content = document.getElementById('viewModalContent');

            content.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center gap-4 pb-4 border-b">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(patient.full_name)}&background=0ea5e9&color=fff&size=80"
                         class="w-20 h-20 rounded-full" alt="Patient">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-800">${patient.full_name}</h4>
                        <p class="text-sm text-gray-500">Patient ID: PT-${String(patient.id).padStart(4, '0')}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="text-sm text-gray-900">${patient.email || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-sm text-gray-900">${patient.phone || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Date of Birth</p>
                        <p class="text-sm text-gray-900">${formatDate(patient.date_of_birth)} (${age} years)</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Gender</p>
                        <p class="text-sm text-gray-900">${capitalizeFirst(patient.gender)}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="text-sm text-gray-900">${patient.address || 'Not provided'}</p>
                    </div>
                    ${patient.last_appointment ? `
                                                                                                                                                                            <div class="md:col-span-2 pt-4 border-t">
                                                                                                                                                                                <p class="text-sm font-medium text-gray-500 mb-2">Last Appointment</p>
                                                                                                                                                                                <p class="text-sm text-gray-900">Date: ${formatDate(patient.last_appointment.date)}</p>
                                                                                                                                                                                <p class="text-sm text-gray-900">Doctor: ${patient.last_appointment.doctor}</p>
                                                                                                                                                                            </div>
                                                                                                                                                                        ` : ''}
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button onclick="closeViewModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                    <button onclick="openEditModal(${patient.id})" 
                            class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                        Edit Patient
                    </button>
                </div>
            </div>
        `;
        }

        function openEditModal(id) {
            closeViewModal();

            if (currentPatient && currentPatient.id === id) {
                populateEditForm(currentPatient);
            } else {
                fetch(`{{ url('frontdesk/patients') }}/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentPatient = data.patient;
                            populateEditForm(data.patient);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading patient:', error);
                        alert('Failed to load patient details');
                    });
            }
        }

        function populateEditForm(patient) {
            document.getElementById('edit_patient_id').value = patient.id;
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_email').value = patient.email;
            document.getElementById('edit_phone').value = patient.phone;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth.split('T')[0];
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_address').value = patient.address || '';

            document.getElementById('editModal').classList.remove('hidden');
        }

        const form = document.getElementById('editPatientForm');

        form.addEventListener('submit', handleEditSubmit);

        // Remove error messages on input/change
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => removeError(field));
            field.addEventListener('change', () => removeError(field));
        });

        function removeError(field) {
            const errorId = field.dataset.error;
            const errorSpan = document.getElementById(errorId);
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.add('hidden');
            }
            field.classList.remove('border-red-500');
        }

        function handleEditSubmit(e) {
            e.preventDefault();

            const id = document.getElementById('edit_patient_id').value;

            const formData = {
                first_name: document.getElementById('edit_first_name').value,
                last_name: document.getElementById('edit_last_name').value,
                email: document.getElementById('edit_email').value,
                phone: document.getElementById('edit_phone').value,
                date_of_birth: document.getElementById('edit_date_of_birth').value,
                gender: document.getElementById('edit_gender').value,
                address: document.getElementById('edit_address').value,
                blood_group: document.getElementById('edit_blood_group').value,
                emergency_contact_name: document.getElementById('edit_emergency_contact_name').value,
                emergency_contact_phone: document.getElementById('edit_emergency_contact_phone').value,
            };

            const saveBtn = document.getElementById('savePatientBtn');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            fetch(`{{ url('frontdesk/patients') }}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                })
                .then(async response => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Changes';

                    if (response.status === 422) {
                        const data = await response.json();
                        showFieldErrors(data.errors);
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        toastr.success(data.message);
                        closeEditModal();
                        loadPatients(currentPage);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function showFieldErrors(errors) {
            // Clear previous errors first
            form.querySelectorAll('input, select, textarea').forEach(field => removeError(field));

            for (const fieldName in errors) {
                const field = document.getElementById(`edit_${fieldName}`);
                if (field) {
                    const errorId = field.dataset.error;
                    const errorSpan = document.getElementById(errorId);
                    if (errorSpan) {
                        errorSpan.textContent = errors[fieldName][0]; // show first error
                        errorSpan.classList.remove('hidden');
                        field.classList.add('border-red-500');
                    }
                }
            }
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentPatient = null;
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editPatientForm').reset();
        }

        // Helper functions
        function calculateAge(dateOfBirth) {
            if (!dateOfBirth) return 'N/A';
            const today = new Date();
            const birthDate = new Date(dateOfBirth);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function capitalizeFirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>
@endpush
