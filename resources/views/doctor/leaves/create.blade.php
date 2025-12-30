@extends('layouts.doctor')

@section('title', 'My Leaves')
@section('page-title', 'My Leaves')

@section('content')

    <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Leave Management</h3>
                <p class="text-sm text-gray-500">View your leave history and apply for new leaves</p>
            </div>
            <button type="button" id="toggleFormBtn"
                class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Apply for Leave
            </button>
        </div>

        <!-- Apply Leave Form (Hidden by default) -->
        <div id="leaveFormSection" class="hidden mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Request Leave</h4>
                    <button type="button" id="closeFormBtn" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form action="#" method="POST" id="leaveForm" class="space-y-6">
                    @csrf

                    <!-- Leave Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Leave Type</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="leave_type" value="full_day" class="hidden peer" checked>
                                <div
                                    class="peer-checked:border-sky-600 peer-checked:bg-sky-50 border border-gray-200 rounded-xl p-5 text-center transition hover:shadow hover:scale-[1.02]">
                                    <p class="font-medium text-gray-900">Full Day</p>
                                    <p class="text-xs text-gray-500">Whole day leave</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="leave_type" value="half_day" class="hidden peer">
                                <div
                                    class="peer-checked:border-amber-500 peer-checked:bg-amber-50 border border-gray-200 rounded-xl p-5 text-center transition hover:shadow hover:scale-[1.02]">
                                    <p class="font-medium text-gray-900">Half Day</p>
                                    <p class="text-xs text-gray-500">Morning / Evening</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="leave_type" value="custom" class="hidden peer">
                                <div
                                    class="peer-checked:border-purple-600 peer-checked:bg-purple-50 border border-gray-200 rounded-xl p-5 text-center transition hover:shadow hover:scale-[1.02]">
                                    <p class="font-medium text-gray-900">Custom Time</p>
                                    <p class="text-xs text-gray-500">Specific hours</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Approval Type --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Approval Type</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="approval_type" value="auto" id="auto-checkbox">
                                <span class="text-sm text-gray-700">Auto</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="approval_type" value="admin" id="admin-checkbox">
                                <span class="text-sm text-gray-700">Approved By Admin</span>
                            </label>
                        </div>
                    </div>




                    <!-- Date Range -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="start_date"
                                class="w-full border  rounded-xl p-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="end_date"
                                class="w-full border  rounded-xl p-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        </div>
                    </div>

                    <!-- Half Day Slot -->
                    <div id="halfDaySection" class="mb-5 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Half Day Slot</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="half_day_slot" value="morning">
                                <span class="text-sm text-gray-700">Morning</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="half_day_slot" value="evening">
                                <span class="text-sm text-gray-700">Evening</span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Time -->
                    <div id="customTimeSection" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time"
                                class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time"
                                class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span
                                class="text-red-500">*</span></label>
                        <textarea name="reason" rows="3"
                            class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                            placeholder="Enter reason for leave..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4">
                        <button type="button" id="cancelFormBtn"
                            class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="leave_submit_button"
                            class="px-6 py-2 bg-sky-600 text-white rounded-xl font-medium hover:bg-sky-700 transition">Submit
                            Leave Request</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointment Conflict Alert -->
        <div id="appointmentConflictModal"
            class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 animate-fade-in">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        </div>
                        <h2 class="ml-3 text-lg font-semibold text-gray-800">
                            Appointment Conflict
                        </h2>
                    </div>

                    <p id="appointmentConflictText" class="text-gray-700 mb-6">
                        You have appointments scheduled during this leave period.
                    </p>

                    <div class="flex justify-end gap-3">
                        <button type="button" id="closeConflictModal"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700">
                            Cancel
                        </button>

                        <a href="{{ route('doctor.appointments') }}"
                            class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">
                            View Appointments
                        </a>

                        <button type="button" id="proceedLeaveBtn"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                            Proceed Anyway
                        </button>
                    </div>
                </div>
            </div>
        </div>


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
                        <p class="text-xl font-bold text-yellow-600">{{ $leaves->where('status', 'pending')->count() }}</p>
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
                        <p class="text-xl font-bold text-green-600">{{ $leaves->where('status', 'approved')->count() }}</p>
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
                        <p class="text-xl font-bold text-red-600">{{ $leaves->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaves List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="text-lg font-semibold text-gray-800">Leave History</h4>
            </div>

            @if ($leaves->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    End Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th> --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Applied On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($leaves as $index => $leave)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($leave->leave_type == 'full_day')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                                Full Day
                                            </span>
                                        @elseif($leave->leave_type == 'half_day')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Half Day ({{ ucfirst($leave->half_day_slot) }})
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Custom ({{ \Carbon\Carbon::parse($leave->start_time)->format('h:i A') }} -
                                                {{ \Carbon\Carbon::parse($leave->end_time)->format('h:i A') }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('d M, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @php
                                            $days =
                                                \Carbon\Carbon::parse($leave->start_date)->diffInDays(
                                                    \Carbon\Carbon::parse($leave->end_date),
                                                ) + 1;
                                        @endphp
                                        {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                        {{ $leave->reason ?? '-' }}
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($leave->status == 'pending')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-yellow-400"></span>
                                                Pending
                                            </span>
                                        @elseif($leave->status == 'approved')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-400"></span>
                                                Approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-400"></span>
                                                Rejected
                                            </span>
                                        @endif
                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $leave->created_at->format('d M, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="border-t border-gray-100 px-6 py-4 text-sky-600">
                        {{ $leaves->links('pagination::tailwind') }}
                    </div>

                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No leaves found</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't applied for any leave yet.</p>
                    <div class="mt-6">
                        <button type="button" id="applyFirstLeaveBtn"
                            class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Apply for Leave
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#auto-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#admin-checkbox').prop('checked', false); // Uncheck Admin checkbox
                }
            });

            $('#admin-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#auto-checkbox').prop('checked', false); // Uncheck Auto checkbox
                }
            });

            // Toggle form visibility
            function showForm() {
                $('#leaveFormSection').removeClass('hidden');
                $('#toggleFormBtn').html(`
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            `);
                $('html, body').animate({
                    scrollTop: $('#leaveFormSection').offset().top - 100
                }, 300);
            }

            function hideForm() {
                $('#leaveFormSection').addClass('hidden');
                $('#toggleFormBtn').html(`
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Apply for Leave
            `);
                $('#leaveForm')[0].reset();
                $('#halfDaySection').addClass('hidden');
                $('#customTimeSection').addClass('hidden');
            }

            $('#toggleFormBtn').on('click', function() {
                if ($('#leaveFormSection').hasClass('hidden')) {
                    showForm();
                } else {
                    hideForm();
                }
            });

            $('#closeFormBtn, #cancelFormBtn').on('click', function() {
                hideForm();
            });

            $('#applyFirstLeaveBtn').on('click', function() {
                showForm();
            });

            // Toggle leave type sections
            $('input[name="leave_type"]').on('change', function() {
                $('#halfDaySection').addClass('hidden');
                $('#customTimeSection').addClass('hidden');

                if (this.value === 'half_day') {
                    $('#halfDaySection').removeClass('hidden');
                }

                if (this.value === 'custom') {
                    $('#customTimeSection').removeClass('hidden');
                }
            });

            // AJAX submit
            $('#leaveForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                // Clear previous errors
                form.find('.error-text').remove();
                form.find('.border-red-500').removeClass('border-red-500');

                $.ajax({
                    url: "{{ route('doctor.leaves.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        $('#leave_submit_button').prop('disabled', true).text('Submitting...');
                    },
                    success: function(response) {
                        toastr.success(response.message ?? 'Leave request submitted');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

                            Object.keys(errors).forEach(function(key) {
                                let field = form.find(`[name="${key}"]`);

                                // Highlight field
                                field.addClass('border-red-500');

                                // Insert error message after the field
                                if (field.next('.error-text').length === 0) {
                                    field.after(
                                        `<p class="text-red-500 text-sm mt-1 error-text">${errors[key][0]}</p>`
                                    );
                                }
                            });
                            return;
                        }

                        if (xhr.status === 409 && xhr.responseJSON.type ===
                            'appointment_conflict') {
                            $('#appointmentConflictText').text(xhr.responseJSON.message);
                            $('#appointmentConflictModal').removeClass('hidden');
                            return;
                        }

                        if (xhr.status === 422 && xhr.responseJSON.type === 'leave_conflict') {
                            toastr.error(xhr.responseJSON.message);
                            return;
                        }

                        toastr.error('Something went wrong. Please try again.');
                    },
                    complete: function() {
                        $('#leave_submit_button').prop('disabled', false).text(
                            'Submit Leave Request');
                    }
                });
            });

            // Remove error messages and red border when user modifies input/select/textarea
            $('#leaveForm').on('input change', 'input, select, textarea', function() {
                $(this).removeClass('border-red-500');
                $(this).next('.error-text').remove();
            });
        });
        $('#closeConflictModal').on('click', function() {
            $('#appointmentConflictModal').addClass('hidden');
        });

        // Proceed with leave and cancel appointments
        $('#proceedLeaveBtn').on('click', function() {
            let form = $('#leaveForm');
            let formData = form.serialize() + '&cancel_appointments=1';

            $.ajax({
                url: "{{ route('doctor.leaves.store') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                beforeSend: function() {
                    $('#proceedLeaveBtn').prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    $('#appointmentConflictModal').addClass('hidden');
                    toastr.success(response.message ??
                        'Leave request submitted and appointments cancelled');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(error => {
                            toastr.error(error[0]);
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    $('#proceedLeaveBtn').prop('disabled', false).text('Proceed Anyway');
                }
            });
        });
    </script>
@endpush
