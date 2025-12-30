@extends('layouts.admin')

@section('title', 'Leave Management')
@section('page-title', 'Leave Management')

@section('content')

    <!-- Message Container -->
    <div id="messageContainer" class="hidden mb-4 p-4 rounded-lg"></div>

    <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div>
            <!-- Leave Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">Total Leaves</p>
                            <p class="text-xl font-bold text-gray-800">{{ $leaves->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">Pending</p>
                            <p class="text-xl font-bold text-yellow-600">{{ $leaves->where('status', 'pending')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">Approved</p>
                            <p class="text-xl font-bold text-green-600">{{ $leaves->where('status', 'approved')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">Rejected</p>
                            <p class="text-xl font-bold text-red-600">{{ $leaves->where('status', 'rejected')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters -->
        <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 sm:gap-4">

                <!-- Date Range -->
                <div class="md:col-span-2">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <div class="flex items-center space-x-2">
                        <input type="date" id="filterStartDate"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <span class="text-gray-500">to</span>
                        <input type="date" id="filterEndDate"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Leave Type -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                    <select id="filterLeaveType"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="full_day">Full Day</option>
                        <option value="half_day">Half Day</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <!-- Doctor -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Doctor</label>
                    <select id="filterDoctor"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            #
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Doctor
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Leave Type
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">
                            Start Date
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            End Date
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">
                            Duration
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Reason
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Applied On
                        </th>
                        <th
                            class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="leaveTableBody" class="divide-y divide-gray-200">
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

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function loaddoctorsleaves(page = 1) {

                let start_date = document.getElementById('filterStartDate').value;
                let end_date = document.getElementById('filterEndDate').value;
                let leave_type = document.getElementById('filterLeaveType').value;
                let doctor_id = document.getElementById('filterDoctor').value;
                let status = document.getElementById('filterStatus').value;

                let query = `?page=${page}
                &doctor_id=${doctor_id}
                &status=${status}
                &leave_type=${leave_type}
                &start_date=${start_date}
                &end_date=${end_date}`;

                fetch(`{{ route('admin.leaves') }}${query}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {

                        let tbody = document.getElementById("leaveTableBody");
                        tbody.innerHTML = '';

                        if (!res.data || res.data.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="9" class="text-center py-6 text-gray-500">
                                        No Leaves Found
                                    </td>
                                </tr>
                            `;
                            document.getElementById("paginationContainer").innerHTML = '';
                            document.getElementById("paginationInfo").innerHTML = '';
                            return;
                        }

                        res.data.forEach((item, index) => {

                            let statusColor =
                                item.status === 'pending' ? 'bg-yellow-100 text-yellow-600' :
                                item.status === 'approved' ? 'bg-green-100 text-green-600' :
                                'bg-red-100 text-red-600';

                            let startDate = formatDate(item.start_date);
                            let endDate = formatDate(item.end_date);
                            let createdAt = formatDate(item.created_at);

                            let duration = '';
                            if (item.start_date && item.end_date) {
                                let start = new Date(item.start_date);
                                let end = new Date(item.end_date);
                                let days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                                duration = days + (days > 1 ? ' days' : ' day');
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td class="px-4 py-3">
                                        ${(res.pagination.current_page - 1) * res.pagination.per_page + index + 1}
                                    </td>
                                    <td class="px-4 py-3">
                                        Dr. ${item.doctor?.first_name ?? ''} ${item.doctor?.last_name ?? ''}
                                    </td>
                                    <td class="px-4 py-3">${formatLeaveType(item.leave_type)}</td>
                                    <td class="px-4 py-3">${startDate}</td>
                                    <td class="px-4 py-3">${endDate}</td>
                                    <td class="px-4 py-3">${duration}</td>
                                    <td class="px-4 py-3">${item.reason ?? ''}</td>
                                    <td class="px-4 py-3">${createdAt}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs ${statusColor}">
                                            ${item.status}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });

                        updatePagination(res.pagination);
                    });
            }

            // PAGINATION
            function updatePagination(pagination) {
                let container = document.getElementById("paginationContainer");
                container.innerHTML = '';

                container.innerHTML += `
                    <button
                        ${pagination.current_page > 1 ? `onclick="loaddoctorsleaves(${pagination.current_page - 1})"` : ''}
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg
                        ${pagination.current_page > 1
                            ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                            : 'text-gray-400 bg-gray-100 cursor-not-allowed'}"
                        ${pagination.current_page <= 1 ? 'disabled' : ''}>
                        Previous
                    </button>
                `;

                for (let i = 1; i <= pagination.last_page; i++) {
                    container.innerHTML += `
                        <button
                            onclick="loaddoctorsleaves(${i})"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg
                            ${i === pagination.current_page
                                ? 'text-white bg-sky-600'
                                : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}">
                            ${i}
                        </button>
                    `;
                }

                container.innerHTML += `
                    <button
                        ${pagination.current_page < pagination.last_page ? `onclick="loaddoctorsleaves(${pagination.current_page + 1})"` : ''}
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg
                        ${pagination.current_page < pagination.last_page
                            ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                            : 'text-gray-400 bg-gray-100 cursor-not-allowed'}"
                        ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}>
                        Next
                    </button>
                `;

                document.getElementById("paginationInfo").innerHTML = `
                    Showing <span class="font-medium">${pagination.from}</span>
                    to <span class="font-medium">${pagination.to}</span>
                    of <span class="font-medium">${pagination.total}</span> results
                `;
            }

            function formatDate(date) {
                if (!date) return '';
                let d = new Date(date);
                return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}`;
            }

            function formatLeaveType(type) {
                return type ? type.replace('_', ' ').toUpperCase() : '';
            }

            // Filter change events
            ['filterStartDate', 'filterEndDate', 'filterLeaveType', 'filterDoctor', 'filterStatus']
            .forEach(id => {
                document.getElementById(id).addEventListener('change', () => loaddoctorsleaves(1));
            });

            // Initial load
            loaddoctorsleaves();
        });
    </script>
@endpush
