@extends('layouts.admin')

@section('title', 'Patients Management')

@section('page-title', 'Patients Management')

@section('content')
    <!-- Search & Filters -->
    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="md:col-span-2">
                <input type="text" id="searchInput" placeholder="Search by name, ID, phone..."
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            <div>
                <select id="bloodGroupFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Blood Groups</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
            <div>
                <select id="statusFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
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
        <p class="mt-2 text-sm text-gray-600">Loading patients...</p>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Patient ID</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Patient</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                            Age/Gender</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                            Blood Group</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                            Phone</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                            Last Visit</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody" class="divide-y divide-gray-200">
                    @php
                        $avatarColors = [
                            'bg-sky-100 text-sky-600',
                            'bg-purple-100 text-purple-600',
                            'bg-emerald-100 text-emerald-600',
                            'bg-pink-100 text-pink-600',
                            'bg-amber-100 text-amber-600',
                            'bg-indigo-100 text-indigo-600',
                        ];
                    @endphp

                    @forelse ($patients as $index => $patient)
                        @php
                            $colorClass = $avatarColors[$index % count($avatarColors)];
                            $initials = strtoupper(
                                substr($patient->user?->first_name, 0, 1) . substr($patient->user?->last_name, 0, 1),
                            );
                            $age = $patient->user?->date_of_birth
                                ? \Carbon\Carbon::parse($patient->user?->date_of_birth)->age
                                : 'N/A';
                            $lastVisit = $patient->appointments()->latest()->first();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-medium text-sky-600">#PT{{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 {{ $colorClass }} rounded-full flex items-center justify-center font-semibold text-sm">
                                        {{ $initials }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-800">{{ $patient->user?->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $patient->user?->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <p class="text-sm text-gray-800">{{ $age }} /
                                    {{ ucfirst($patient->user?->gender ?? 'N/A') }}</p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if ($patient->blood_group)
                                    <span
                                        class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">{{ $patient->blood_group }}</span>
                                @else
                                    <span class="text-sm text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <p class="text-sm text-gray-800">{{ $patient->user?->phone ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if ($lastVisit)
                                    <p class="text-sm text-gray-800">{{ $lastVisit->appointment_date->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-500">{{ $lastVisit->appointment_date->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">No visits</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($patient->user?->status === 'active')
                                    <span
                                        class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Active</span>
                                @elseif($patient->user?->status === 'inactive')
                                    <span
                                        class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">Inactive</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs font-medium text-amber-700 bg-amber-100 rounded-full">{{ ucfirst($patient->user?->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button onclick="viewPatient({{ $patient->id }})"
                                        class="text-sky-600 hover:text-sky-800" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button onclick="editPatient({{ $patient->id }})"
                                        class="text-amber-600 hover:text-amber-800" title="Edit Patient">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deletePatient({{ $patient->user?->id }})"
                                        class="text-red-600 hover:text-red-800" title="Delete Patient">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No patients found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what
                                    you're looking for.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
            <div class="text-xs sm:text-sm text-gray-600">
                Showing <span class="font-medium" id="paginationFrom">{{ $patients->firstItem() ?? 0 }}</span> to <span
                    class="font-medium" id="paginationTo">{{ $patients->lastItem() ?? 0 }}</span> of <span
                    class="font-medium" id="paginationTotal">{{ $patients->total() }}</span> patients
            </div>
            <div id="paginationButtons" class="flex flex-wrap gap-2 justify-center">
                @if ($patients->onFirstPage())
                    <button disabled
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">Previous</button>
                @else
                    <button onclick="goToPage({{ $patients->currentPage() - 1 }})"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                @endif

                @for ($i = 1; $i <= $patients->lastPage(); $i++)
                    @if ($i == $patients->currentPage())
                        <button
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-sky-600 rounded-lg">{{ $i }}</button>
                    @else
                        <button onclick="goToPage({{ $i }})"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $i }}</button>
                    @endif
                @endfor

                @if ($patients->hasMorePages())
                    <button onclick="goToPage({{ $patients->currentPage() + 1 }})"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                @else
                    <button disabled
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">Next</button>
                @endif
            </div>
        </div>
    </div>

    <!-- View Patient Modal -->
    <div id="viewPatientModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Patient Details</h3>
                <button onclick="closePatientViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="viewPatientContent" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div id="editPatientModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Edit Patient</h3>
                <button onclick="closePatientEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="editPatientContent" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
                </div>
            </div>
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

    <script>
        let searchTimeout;
        let currentPage = 1;
        let patientToDeleteId = null;

        function goToPage(page) {
            currentPage = page;
            fetchPatients();
        }

        function closeModal() {
            document.getElementById('viewPatientModal').classList.add('hidden');
            document.getElementById('editPatientModal').classList.add('hidden');
            currentPatient = null;
        }
        // View Patient Details
        function viewPatient(patientId) {
            const modal = document.getElementById('viewPatientModal');
            modal.classList.remove('hidden');

            fetch(`/admin/patients/${patientId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('viewPatientContent').innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('viewPatientContent').innerHTML =
                        '<div class="text-center py-8 text-red-600">Failed to load patient details</div>';
                });
        }

        function closePatientViewModal() {
            document.getElementById('viewPatientModal').classList.add('hidden');
        }

        // Edit Patient
        function editPatient(patientId) {
            const modal = document.getElementById('editPatientModal');
            modal.classList.remove('hidden');

            fetch(`/admin/patients/${patientId}/edit`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editPatientContent').innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('editPatientContent').innerHTML =
                        '<div class="text-center py-8 text-red-600">Failed to load edit form</div>';
                });
        }

        function deletePatient(patientId) {
            patientToDeleteId = patientId;
            document.getElementById('deletePatientModal').classList.remove('hidden');
        }

        // Cancel delete button
        document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
            patientToDeleteId = null;
            document.getElementById('deletePatientModal').classList.add('hidden');
        });

        // Close modal when clicking outside
        document.getElementById('deletePatientModal').addEventListener('click', (e) => {
            if (e.target.id === 'deletePatientModal') {
                patientToDeleteId = null;
                document.getElementById('deletePatientModal').classList.add('hidden');
            }
        });

        // Confirm delete button
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            if (!patientToDeleteId) return;

            const patientId = patientToDeleteId;
            document.getElementById('deletePatientModal').classList.add('hidden');
            patientToDeleteId = null;

            fetch(`/admin/patients/${patientId}`, {
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
                        toastr.success(data.message || 'Patient deleted successfully!');
                        fetchPatients(); // Refresh the patient list
                    } else {
                        toastr.error(data.message || 'Failed to delete patient');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while deleting the patient. Please try again.');
                });
        });

        function closePatientEditModal() {
            document.getElementById('editPatientModal').classList.add('hidden');
        }

        // Save edited patient
        function savePatient(patientId) {
            const formData = new FormData(document.getElementById('editPatientForm'));

            // Clear all previous errors
            clearFormErrors();

            fetch(`/admin/patients/${patientId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw {
                                status: response.status,
                                data: data
                            };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closePatientEditModal();
                        fetchPatients();
                        toastr.success(data.message);

                    } else {
                        toastr.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    if (error.status === 422 && error.data.errors) {
                        // Display inline validation errors
                        displayFormErrors(error.data.errors);
                    } else if (error.data && error.data.message) {
                        toastr.error(error.data.message);
                    } else {
                        toastr.error('An error occurred while saving. Please try again.');
                    }
                });
        }

        function clearFormErrors() {
            // Remove error styling from all inputs
            const inputs = document.querySelectorAll(
                '#editPatientForm input, #editPatientForm select, #editPatientForm textarea');
            inputs.forEach(input => {
                input.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                input.classList.add('border-gray-300', 'focus:ring-sky-500', 'focus:border-transparent');
            });

            // Hide all error messages
            const errorMessages = document.querySelectorAll('#editPatientForm .error-message');
            errorMessages.forEach(msg => {
                msg.classList.add('hidden');
                msg.textContent = '';
            });
        }

        function displayFormErrors(errors) {
            Object.keys(errors).forEach(fieldName => {
                const input = document.querySelector(`#editPatientForm [name="${fieldName}"]`);
                if (input) {
                    // Add error styling to input
                    input.classList.remove('border-gray-300', 'focus:ring-sky-500',
                        'focus:border-transparent');
                    input.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');

                    // Show error message below input
                    const errorContainer = input.parentElement.querySelector('.error-message');
                    if (errorContainer) {
                        errorContainer.textContent = errors[fieldName][0]; // Show first error message
                        errorContainer.classList.remove('hidden');
                    }

                    // Scroll to first error
                    if (Object.keys(errors)[0] === fieldName) {
                        input.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        }

        function fetchPatients() {
            const search = document.getElementById('searchInput').value;
            const blood_group = document.getElementById('bloodGroupFilter').value;
            const status = document.getElementById('statusFilter').value;

            // Show loading indicator
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.querySelector('.overflow-x-auto').style.opacity = '0.5';

            // Build query parameters
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (blood_group) params.append('blood_group', blood_group);
            if (status) params.append('status', status);
            params.append('page', currentPage);

            // Make AJAX request
            fetch(`{{ route('admin.patients') }}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('patientTableBody').innerHTML = data.html;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.querySelector('.overflow-x-auto').style.opacity = '1';

                    // Update pagination info
                    // Prevent crash if pagination is missing
                    if (data.pagination) {
                        document.getElementById('paginationFrom').textContent = data.pagination.from ?? 0;
                        document.getElementById('paginationTo').textContent = data.pagination.to ?? 0;
                        document.getElementById('paginationTotal').textContent = data.pagination.total ?? 0;

                        updatePaginationButtons(data.pagination);
                    } else {
                        console.error("Pagination missing from response:", data);
                    }


                    // Update pagination buttons
                    updatePaginationButtons(data.pagination);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.querySelector('.overflow-x-auto').style.opacity = '1';
                    console.log('error->>>>', error);
                    toastr.error('An error occurred while fetching patients. Please try again.');
                });
        }

        function updatePaginationButtons(pagination) {
            let buttonsHtml = '';

            // Previous button
            if (pagination.current_page === 1) {
                buttonsHtml +=
                    '<button disabled class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">Previous</button>';
            } else {
                buttonsHtml +=
                    `<button onclick="goToPage(${pagination.current_page - 1})" class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    buttonsHtml +=
                        `<button class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-sky-600 rounded-lg">${i}</button>`;
                } else {
                    buttonsHtml +=
                        `<button onclick="goToPage(${i})" class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">${i}</button>`;
                }
            }

            // Next button
            if (pagination.current_page === pagination.last_page) {
                buttonsHtml +=
                    '<button disabled class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">Next</button>';
            } else {
                buttonsHtml +=
                    `<button onclick="goToPage(${pagination.current_page + 1})" class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>`;
            }

            document.getElementById('paginationButtons').innerHTML = buttonsHtml;
        }

        // Search input with debounce
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            currentPage = 1; // Reset to first page on search
            searchTimeout = setTimeout(() => {
                fetchPatients();
            }, 500);
        });

        // Blood group filter
        document.getElementById('bloodGroupFilter').addEventListener('change', function() {
            currentPage = 1; // Reset to first page on filter
            fetchPatients();
        });

        // Status filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            currentPage = 1; // Reset to first page on filter
            fetchPatients();
        });

        // Close modals when clicking outside (on the backdrop)
        document.getElementById('editPatientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePatientEditModal();
            }
        });

        document.getElementById('viewPatientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePatientViewModal();
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePatientEditModal();
                closePatientViewModal();
            }
        });
    </script>
@endsection
