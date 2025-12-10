@extends('layouts.admin')

@section('title', 'Specialities Management')
@section('page-title', 'Specialities Management')

@section('header-actions')
    <a href="javascript:void(0)" data-id=""
        class="px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium openaddmodal">
        + Add Speciality
    </a>
@endsection

@section('content')

    <!-- Message Container -->
    <div id="messageContainer" class="hidden mb-4 p-4 rounded-lg"></div>

    <!-- Filters -->
    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search Speciality..."
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusInput"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="applyFiltersBtn" onclick="loadspecialities()"
                    class="w-full px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base text-white bg-sky-600 hover:bg-sky-700 rounded-lg font-medium">Apply
                    Filters</button>
            </div>
        </div>
    </div>

    <!-- specialities Table -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Speciality</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                            Description</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Created at</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                            Updated at</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="specialitiesTableBody" class="divide-y divide-gray-200">
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
    </div>

    <!-- View Details Modal -->
    <div class="view_modal hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Modal Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeViewModal()"></div>

            <!-- Modal Container -->
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full relative z-50 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Specialty Details</h3>
                    <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="viewmodalbody p-4 sm:p-6">
                    <!-- Dynamic content loads here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Delete Modal -->
    <div id="customDeleteModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h2 class="text-lg font-semibold mb-4">Confirm Delete</h2>
            <p id="deleteModalText" class="text-gray-700 mb-6">Are you sure you want to delete this specialty?</p>

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

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let deleteId = null;
                let deleteName = null;

                // When delete button is clicked → OPEN MODAL
                $(document).on('click', '.specialities-destroy', function() {
                    deleteId = $(this).data('id');
                    deleteName = $(this).data('name');

                    // Update the modal text with the specialty name
                    document.getElementById('deleteModalText').textContent =
                        `Are you sure you want to delete the specialty "${deleteName}"?`;

                    document.getElementById('customDeleteModal').classList.remove('hidden');
                });

                // Cancel button → CLOSE MODAL
                document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
                    document.getElementById('customDeleteModal').classList.add('hidden');
                    deleteId = null;
                    deleteName = null;
                });

                // Function to get the current filter values
                function getFilters() {
                    return {
                        search: document.getElementById("searchInput").value,
                        status: document.getElementById("statusInput").value,
                    };
                }

                // Helper function to format dates to "DD-MM-YYYY"
                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                }

                // Function to load specialties data and update the table
                window.loadspecialities = function(page = 1) {
                    let filters = getFilters();

                    // Build query string for filters
                    let query = `?page=${page}`;
                    if (filters.search) query += `&search=${filters.search}`;
                    if (filters.status) query += `&status=${filters.status}`;

                    // Fetch data from the server
                    fetch("{{ route('admin.specialities-list') }}" + query)
                        .then(response => response.json())
                        .then(res => {
                            let data = res.data;
                            let tbody = document.getElementById("specialitiesTableBody");
                            let paginationContainer = document.getElementById("paginationContainer");
                            let paginationInfo = document.getElementById("paginationInfo");

                            if (tbody) {
                                tbody.innerHTML = "";
                            }

                            // If no data found
                            if (data.data.length === 0) {
                                if (tbody) {
                                    tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">No Specialty Found</td>
                            </tr>`;
                                }
                                if (paginationContainer) paginationContainer.innerHTML = '';
                                if (paginationInfo) paginationInfo.innerHTML = '';
                                return;
                            }

                            // Loop through data and add rows to table
                            data.data.forEach((item, index) => {
                                let statusColor = item.status === 'active' ?
                                    'bg-green-100 text-green-700' :
                                    'bg-red-100 text-red-700';

                                // Format the created_at and updated_at dates
                                let createdAt = formatDate(item.created_at);
                                let updatedAt = formatDate(item.updated_at);

                                let row = `
                        <tr class="hover:bg-gray-50" id="specialty-${item.id}">
                            <td class="px-4 py-3 text-gray-800">
                                ${(data.current_page - 1) * data.per_page + index + 1}
                            </td>
                            <td class="px-4 py-3 text-gray-800">${item.name}</td>
                            <td class="px-4 py-3 text-gray-500">${item.description ?? ''}</td>
                            <td class="px-4 py-3 text-gray-500">${createdAt}</td>
                            <td class="px-4 py-3 text-gray-500">${updatedAt}</td>
                            <td class="px-4 py-3">
                                <span class="status-badge px-3 py-1 rounded-full text-xs font-medium cursor-pointer ${statusColor}" data-id="${item.id}" data-status="${item.status}">
                                    ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex space-x-1 sm:space-x-2">
                                    <button class="text-sky-600 hover:text-sky-800 view-appointment-btn" data-id="${item.id}">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button class="text-amber-600 hover:text-amber-800 openaddmodal" data-id="${item.id}">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 specialities-destroy" data-id="${item.id}" data-name="${item.name}">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>`;

                                tbody.innerHTML += row;
                            });

                            // Update pagination
                            updatePagination(data);
                        })
                        .catch(error => {
                            console.error("Error loading specialties:", error);
                        });
                }

                // Pagination update function
                function updatePagination(data) {
                    let paginationHTML = "";

                    // Previous Button
                    paginationHTML += `
                        <button onclick="loadspecialities(${data.prev_page_url ? data.current_page - 1 : data.current_page})"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${data.prev_page_url ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' : 'text-gray-400 bg-gray-100 cursor-not-allowed'} rounded-lg">
                            Previous
                        </button>
                    `;

                    // Page Numbers
                    for (let i = 1; i <= data.last_page; i++) {
                        paginationHTML += `
                            <button onclick="loadspecialities(${i})"
                                class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${i === data.current_page ? 'text-white bg-sky-600' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'} rounded-lg">
                                ${i}
                            </button>`;
                    }

                    // Next Button
                    paginationHTML += `
                        <button onclick="loadspecialities(${data.next_page_url ? data.current_page + 1 : data.current_page})"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm ${data.next_page_url ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' : 'text-gray-400 bg-gray-100 cursor-not-allowed'} rounded-lg">
                            Next
                        </button>
                    `;

                    document.querySelector("#paginationContainer").innerHTML = paginationHTML;

                    // Update pagination info
                    document.querySelector("#paginationInfo").innerHTML =
                        `Showing <span class="font-medium">${data.from}</span> to <span class="font-medium">${data.to}</span> of <span class="font-medium">${data.total}</span> results`;
                }

                $('body').on('click', '.openaddmodal', function() {
                    var id = $(this).data('id');
                    if (id == '' || id == undefined) {
                        $('.modal-title').text("Add Specialty");
                    } else {
                        $('.modal-title').text("Edit Specialty");
                    }

                    $.ajax({
                        url: "{{ route('admin.specialities-getmodel') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: id
                        },
                        success: function(data) {
                            $('.addmodalbody').html(data);
                            $('.add_modal').removeClass('hidden');
                        },
                    });
                });

                // Function to close specialty modal
                window.closeSpecialtyModal = function() {
                    $('.add_modal').addClass('hidden');
                    $('.addmodalbody').html('');
                };

                $('body').on('submit', '.specialities-formsubmit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: $(this).attr('action'),
                        data: new FormData(this),
                        type: 'POST',
                        contentType: false,
                        cache: false,
                        processData: false,

                        success: function(data) {
                            // CLEAR OLD ERRORS
                            $('.error').text('');
                            $('input, select').removeClass('border-red-500');

                            if (data.status == 400) {
                                $.each(data.errors, function(field, messages) {
                                    $('.' + field + '_error').text(messages[0]);
                                    $('[name="' + field + '"]').addClass('border-red-500');
                                });
                                return;
                            }
                            if (data.status == 200) {
                                toastr.success(data.msg);
                                // close modal
                                closeSpecialtyModal();
                                loadspecialities();
                            }
                        }
                    });
                });

                // REMOVE ERROR WHILE TYPING
                $('body').on('input change', 'input, select', function() {
                    let name = $(this).attr('name');
                    $('.' + name + '_error').text('');
                    $(this).removeClass('border-red-500');
                });

                $('body').on('click', '.status-badge', function() {
                    let badge = $(this);
                    let id = badge.data('id');
                    let currentStatus = badge.data('status');

                    $.ajax({
                        url: '{{ route('admin.specialities-toggleStatus') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.msg);

                                // Update the badge immediately without page reload
                                let newStatus = response.new_status;
                                let newColor = newStatus === 'active' ?
                                    'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                                let oldColor = currentStatus === 'active' ?
                                    'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                                badge.removeClass(oldColor);
                                badge.addClass(newColor);
                                badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                                badge.data('status', newStatus);
                            }
                        },
                        error: function() {
                            toastr.error('Server error. Please try again.');
                        }
                    });
                });

                // View specialty details
                $('body').on('click', '.view-appointment-btn', function() {
                    let id = $(this).data('id');

                    $.ajax({
                        url: '{{ route('admin.specialities-view', ':id') }}'.replace(':id', id),
                        type: 'GET',
                        success: function(response) {
                            $('.viewmodalbody').html(response);
                            $('.view_modal').removeClass('hidden');
                        },
                        error: function() {
                            toastr.error('Failed to load specialty details.');
                        }
                    });
                });

                // Confirm delete button
                document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                    if (!deleteId) return;
                    $.ajax({
                        url: '{{ route('admin.specialities-destroy', ':id') }}'.replace(':id',
                            deleteId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                toastr.success(data.msg);

                                // Remove the specialty from the list without reloading the page
                                $('#specialty-' + deleteId).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            } else {
                                toastr.error(data.msg);
                            }

                            document.getElementById('customDeleteModal').classList.add('hidden');
                            deleteId = null;
                            deleteName = null;
                        },
                        error: function(xhr, status, error) {
                            if (xhr.status === 403) {
                                toastr.error('You are not authorized to delete this specialty.');
                            } else {
                                toastr.error('Failed to delete specialty. Please try again.');
                            }

                            document.getElementById('customDeleteModal').classList.add('hidden');
                            deleteId = null;
                            deleteName = null;
                        }
                    });
                });

                // Function to close view modal
                window.closeViewModal = function() {
                    $('.view_modal').addClass('hidden');
                    $('.viewmodalbody').html('');
                };

                // Initial page load
                loadspecialities();
            });
        </script>
    @endpush
@endsection
