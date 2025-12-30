@extends('layouts.doctor')

@section('title', 'My Appointments')

@section('page-title', 'My Appointments')

@section('content')
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input id="filterDate" type="date"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="filterStatus"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Patient</label>
                <input id="filterSearch" type="text" placeholder="Search by name or appointment #"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div class="flex items-end">
                <button id="filterBtn"
                    class="w-full px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b">
            <h3 id="appointmentsHeader" class="text-base sm:text-lg font-semibold text-gray-800">Today's Appointments -
                <span id="appointmentsDate">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</span>
            </h3>
        </div>

        <!-- Appointment Cards (dynamic) -->
        <div id="appointmentsContainer" class="divide-y">
            <div class="p-6 text-center text-sm text-gray-500">Loading appointments…</div>
        </div>
    </div>

    <!-- Confirmation Modal (hidden by default) -->
    <div id="confirmCompleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md mx-auto z-10">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold">Confirm Completion</h3>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-700">Are you sure you want to mark this appointment as completed?</p>
            </div>
            <div class="p-4 border-t flex justify-end gap-3">
                <button id="cancelCompleteBtn" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button id="confirmCompleteBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Yes,
                    Complete</button>
            </div>
        </div>
    </div>

    <!-- Pagination (dynamic) -->
    <div id="appointmentsPagination" class="mt-4 sm:mt-6"></div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Reschedule Appointment</h3>

            <input type="hidden" id="rescheduleAppointmentId">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Select Date</label>
                <input type="date" id="rescheduleDate" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Select Time</label>
                <select id="rescheduleTime" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Loading available times...</option>
                </select>
                <p id="slotsLoading" class="text-sm text-gray-500 mt-2 hidden">Loading available times…</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Note (Optional)</label>
                <textarea id="rescheduleNote" class="w-full border rounded-lg px-3 py-2" name="note"
                    placeholder="Add a reason or note for rescheduling" rows="3" maxlength="100"></textarea>
                <div class="flex justify-between mt-1">
                    <p id="rescheduleNoteError" class="text-red-500 text-xs hidden">Note cannot exceed 100 characters</p>
                    <p id="rescheduleNoteCount" class="text-xs text-gray-500">0/100</p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button id="closeRescheduleModal" class="px-4 py-2 bg-gray-200 rounded-lg">
                    Cancel
                </button>
                <button id="submitReschedule" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                    Reschedule
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm flex flex-col">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Cancel Appointment?
            </h3>
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to cancel this appointment?
            </p>

            <!-- Cancellation Reason Textarea -->
            <textarea id="cancellationReason" class="w-full p-2 border border-gray-300 rounded-lg mb-2"
                placeholder="Please provide a reason for cancellation (optional)" rows="4"></textarea>

            <!-- Error message below textarea -->
            <p id="cancellationReasonError" class="text-red-500 text-xs hidden mt-2">Please provide a reason for
                cancellation.</p>

            <!-- Footer with Cancel and Ok buttons -->
            <div class="flex justify-end gap-3 mt-4">
                <button id="closeCancelModal" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                    No
                </button>
                <button id="confirmCancel" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const $container = $('#appointmentsContainer');
                const $headerDate = $('#appointmentsDate');
                const $pagination = $('#appointmentsPagination');

                // Configure toastr (we rely on Toastr loaded in layout)
                if (window.toastr) {
                    window.toastr.options = window.toastr.options || {};
                    window.toastr.options.closeButton = true;
                    window.toastr.options.progressBar = true;
                    window.toastr.options.positionClass = window.toastr.options.positionClass || 'toast-top-right';
                }

                function statusBadge(status) {
                    const map = {
                        pending: ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                        confirmed: ['bg-green-100', 'text-green-700', 'Confirmed'],
                        checked_in: ['bg-indigo-100', 'text-indigo-700', 'Checked In'],
                        in_progress: ['bg-sky-100', 'text-sky-700', 'In Progress'],
                        completed: ['bg-blue-100', 'text-blue-700', 'Completed'],
                        cancelled: ['bg-red-100', 'text-red-700', 'Cancelled'],
                        no_show: ['bg-gray-100', 'text-gray-700', 'No Show']
                    };
                    return map[status] || ['bg-gray-100', 'text-gray-700', (status || 'Unknown')];
                }

                function renderCard(a) {
                    const badge = statusBadge(a.status);
                    const avatar = encodeURIComponent(a.patient_name || 'Patient');
                    const ampm = a.time && a.time.includes('AM') ? 'AM' : (a.time && a.time.includes('PM') ? 'PM' : '');

                    return `
                    <div id="appointment-card-${a.id}" class="p-4 sm:p-6 hover:bg-gray-50 ${a.status === 'completed' ? 'opacity-60' : ''}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 flex-1">
                                <div class="w-16 h-16 ${(a.status === 'completed') ? 'bg-gray-100' : 'bg-sky-100'} rounded-lg flex flex-col items-center justify-center shrink-0">
                                    <span class="text-xs ${(a.status === 'completed') ? 'text-gray-600' : 'text-sky-600'} font-medium">${a.time ? a.time.replace(/\s?(AM|PM)$/, '') : ''}</span>
                                    <span class="text-xs text-gray-500">${ampm}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2">
                                        <img src="https://ui-avatars.com/api/?name=${avatar}&background=60a5fa&color=fff" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full" alt="Patient">
                                        <div>
                                            <h4 class="text-base sm:text-lg font-semibold text-gray-800">${a.patient_name || '—'}</h4>
                                            <p class="text-xs sm:text-sm text-gray-500">
                                                ID: ${a.appointment_number ? a.appointment_number : '—'}
                                                ${a.patient_age ? (' • Age: ' + a.patient_age) : ''}
                                                ${a.patient_gender ? (' • ' + (a.patient_gender.charAt(0).toUpperCase() + a.patient_gender.slice(1))) : ''}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <p class="text-xs sm:text-sm text-gray-700"><span class="font-medium">Reason:</span> ${a.reason || ''}</p>
                                        <p class="text-xs sm:text-sm text-gray-700"><span class="font-medium">Phone:</span> ${a.patient_phone || ''}</p>
                                        <p class="text-xs sm:text-sm text-gray-700"><span class="font-medium">Allergies:</span> ${a.patient_allergies || 'None'}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-start lg:items-end gap-2">
                                <span id="status-badge-${a.id}" class="px-3 py-1 ${badge[0]} ${badge[1]} text-xs sm:text-sm font-medium rounded-full">${badge[2]}</span>
                                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto mt-2">
                                    <a href="${a.details_url || '#'}" class="px-3 sm:px-4 py-2 bg-sky-600 text-white text-xs sm:text-sm rounded-lg hover:bg-sky-700 text-center">View Details</a>

                                    ${(a.status !== 'completed' && a.status !== 'cancelled' && a.status !== 'in_progress' && a.status !== 'checked_in') ? `<button data-id="${a.id}" data-date="${a.date || ''}" data-time="${a.time || ''}" class="btn-reschedule px-3 sm:px-4 py-2 bg-white border border-sky-600 text-sky-600 text-xs sm:text-sm rounded-lg hover:bg-sky-50">Reschedule</button>` : ''}

                                    ${(a.status !== 'completed' && a.status !== 'cancelled' && a.status !== 'in_progress' && a.status !== 'checked_in') ? `<button data-id="${a.id}" class="btn-cancel px-3 sm:px-4 py-2 bg-red-600 text-white text-xs sm:text-sm rounded-lg hover:bg-red-700">Cancel</button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }

                $container.on('click', '.btn-reschedule', function() {
                    const id = $(this).data('id');
                    const apptDate = $(this).data('date') || '';
                    const apptTime = $(this).data('time') || '';

                    // set appointment id
                    $('#rescheduleAppointmentId').val(id);

                    // compute default date: use appointment date if today or future, otherwise today
                    const todayStr = new Date().toISOString().slice(0, 10);
                    let defaultDate = apptDate && apptDate >= todayStr ? apptDate : todayStr;

                    // set min date to today (prevent selecting past dates)
                    $('#rescheduleDate').attr('min', todayStr).val(defaultDate);

                    // clear time select while we fetch
                    $('#rescheduleTime').empty().append('<option value="">Loading available times...</option>');
                    $('#slotsLoading').removeClass('hidden');

                    // clear note field
                    $('#rescheduleNote').val('');
                    $('#rescheduleNoteCount').text('0/100');
                    $('#rescheduleNoteError').addClass('hidden');

                    // show modal
                    $('#rescheduleModal').removeClass('hidden').addClass('flex');

                    // fetch available slots for defaultDate and select appointment time if available
                    fetchAvailableSlots(defaultDate, apptTime);
                });

                // When date changes in modal, fetch slots for that date
                $('#rescheduleDate').on('change', function() {
                    const date = $(this).val();
                    if (!date) return;
                    $('#rescheduleTime').empty().append('<option value="">Loading available times...</option>');
                    $('#slotsLoading').removeClass('hidden');
                    fetchAvailableSlots(date, null);
                });

                // Parse time like '10:30 AM' into Date object for given YYYY-MM-DD
                function parseTimeToDate(dateStr, timeStr) {
                    if (!timeStr || !dateStr) return null;
                    const m = timeStr.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
                    if (!m) return null;
                    let hh = parseInt(m[1], 10);
                    const mm = parseInt(m[2], 10);
                    const ap = m[3];
                    if (ap) {
                        if (/pm/i.test(ap) && hh !== 12) hh += 12;
                        if (/am/i.test(ap) && hh === 12) hh = 0;
                    }
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return null;
                    const year = parseInt(parts[0], 10);
                    const month = parseInt(parts[1], 10) - 1;
                    const day = parseInt(parts[2], 10);
                    return new Date(year, month, day, hh, mm, 0, 0);
                }

                // Fetch available slots from server and populate select; filter out past times for today
                function fetchAvailableSlots(date, selectedTime) {
                    const $select = $('#rescheduleTime');
                    const now = new Date();

                    $.get('/doctor/appointments/available-slots', {
                            date: date
                        })
                        .done(function(res) {
                            let slots = [];
                            if (res && res.status === 200 && res.data) {
                                if (Array.isArray(res.data.slots)) slots = res.data.slots;
                                else if (Array.isArray(res.data)) slots = res.data;
                                else if (Array.isArray(res.data.available_slots)) slots = res.data.available_slots;
                            } else if (Array.isArray(res)) {
                                slots = res;
                            } else if (res && Array.isArray(res.slots)) {
                                slots = res.slots;
                            }

                            // filter out past times if date is today
                            const isToday = (date === now.toISOString().slice(0, 10));
                            const filtered = slots.filter(function(s) {
                                const dt = parseTimeToDate(date, s);
                                if (!dt) return false;
                                if (isToday) return dt > now;
                                return true;
                            });

                            $select.empty();
                            if (!filtered.length) {
                                $select.append('<option value="">No available slots</option>');
                            } else {
                                filtered.forEach(function(s) {
                                    const sel = (selectedTime && s === selectedTime) ? ' selected' : '';
                                    $select.append('<option value="' + s + '"' + sel + '>' + s + '</option>');
                                });
                            }
                        })
                        .fail(function() {
                            $select.empty().append('<option value="">Unable to load slots</option>');
                        })
                        .always(function() {
                            $('#slotsLoading').addClass('hidden');
                        });
                }

                // Character counter for reschedule note
                $('#rescheduleNote').on('input', function() {
                    const len = $(this).val().length;
                    $('#rescheduleNoteCount').text(len + '/100');
                    if (len > 100) {
                        $('#rescheduleNoteError').removeClass('hidden');
                    } else {
                        $('#rescheduleNoteError').addClass('hidden');
                    }
                });

                $('#closeRescheduleModal').on('click', function() {
                    $('#rescheduleModal')
                        .addClass('hidden')
                        .removeClass('flex');
                });

                $('#submitReschedule').on('click', function() {
                    const id = $('#rescheduleAppointmentId').val();
                    const date = $('#rescheduleDate').val();
                    const time = $('#rescheduleTime').val();
                    const note = $('#rescheduleNote').val().trim();

                    if (!date || !time) {
                        toastr.error('Please select date and time', '', {
                            closeButton: false
                        });
                        return;
                    }

                    if (note.length > 100) {
                        toastr.error('Note cannot exceed 100 characters', '', {
                            closeButton: false
                        });
                        return;
                    }

                    $.ajax({
                        url: `/doctor/appointments/${id}/reschedule`,
                        type: 'POST',
                        data: {
                            date: date,
                            time: time,
                            note: note
                        },
                        success: function(res) {
                            if (res.status === 200) {
                                toastr.success(res.msg, '', {
                                    closeButton: false
                                });

                                $('#rescheduleModal')
                                    .addClass('hidden')
                                    .removeClass('flex');

                                loadAppointments();
                            } else {
                                toastr.error(res.msg, '', {
                                    closeButton: false
                                });
                            }

                            if (res.status === 400) {
                                toastr.success(res.msg, '', {
                                    closeButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON) {
                                const res = xhr.responseJSON;

                                if (res.msg) {
                                    toastr.error(res.msg, '', {
                                        closeButton: false
                                    });
                                } else if (res.errors) {
                                    Object.values(res.errors).forEach(err => {
                                        toastr.error(err[0], '', {
                                            closeButton: false
                                        });
                                    });
                                } else {
                                    toastr.error('Unexpected error occurred', '', {
                                        closeButton: false
                                    });
                                }
                            } else {
                                toastr.error('Server error', '', {
                                    closeButton: false
                                });
                            }
                        }
                    });
                });

                let cancelAppointmentId = null;

                $(document).on('click', '.btn-cancel', function() {
                    cancelAppointmentId = $(this).data('id');
                    $('#cancelModal').removeClass('hidden').addClass('flex');
                    $('#cancellationReason').removeClass('border-red-500');
                    $('#cancellationReasonError').addClass('hidden');
                });

                $('#closeCancelModal').on('click', function() {
                    cancelAppointmentId = null;
                    $('#cancelModal').addClass('hidden').removeClass('flex');
                });

                $('#confirmCancel').on('click', function() {
                    const cancellationReason = $('#cancellationReason').val().trim();

                    if (!cancellationReason) {
                        $('#cancellationReason').addClass('border-red-500');
                        $('#cancellationReasonError').removeClass('hidden').text(
                            'Please provide a reason for cancellation.');
                        return;
                    }

                    if (!cancelAppointmentId) return;

                    $.ajax({
                        url: `/doctor/appointments/${cancelAppointmentId}/cancel`,
                        type: 'POST',
                        data: {
                            cancellation_reason: cancellationReason
                        },
                        success: function(res) {
                            const ok = (res && (res.success === true || res.status === 200 || res
                                .status === '200'));

                            if (!ok) {
                                const msg = (res && (res.message || res.msg || res.error)) ||
                                    'Cancel failed';
                                toastr.error(msg, '', {
                                    closeButton: false
                                });
                                return;
                            }

                            const $card = $(`#appointment-card-${cancelAppointmentId}`);
                            $(`#status-badge-${cancelAppointmentId}`)
                                .removeClass()
                                .addClass(
                                    'px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full'
                                )
                                .text('Cancelled');

                            $card.find('.btn-cancel').remove();
                            $card.find('.btn-reschedule').remove();
                            $card.addClass('opacity-60');
                            $('#cancelModal').addClass('hidden').removeClass('flex');

                            toastr.success(res.message || res.msg || 'Appointment cancelled', '', {
                                closeButton: false
                            });
                            loadAppointments();
                            cancelAppointmentId = null;
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errorMessage = xhr.responseJSON.message ||
                                    'Please provide a reason for cancellation.';
                                $('#cancellationReason').addClass('border-red-500');
                                $('#cancellationReasonError').removeClass('hidden').text(
                                    errorMessage);
                            } else {
                                toastr.error('Server error', '', {
                                    closeButton: false
                                });
                            }
                        }
                    });
                });

                $('#cancellationReason').on('input', function() {
                    if ($(this).val().trim()) {
                        $(this).removeClass('border-red-500');
                        $('#cancellationReasonError').addClass('hidden');
                    }
                });

                function renderPagination(meta) {
                    if (!meta) return $pagination.empty();

                    const current = meta.current_page || 1;
                    const last = meta.last_page || 1;
                    const total = meta.total || 0;
                    const perPage = meta.per_page || 5;

                    const start = total === 0 ? 0 : ((current - 1) * perPage) + 1;
                    const end = Math.min(total, current * perPage);

                    const $wrap = $(
                        '<div class="mt-4 sm:mt-6 flex flex-col sm:flex-row items-center justify-between gap-4"></div>');
                    const $left = $(
                        `<p class="text-xs sm:text-sm text-gray-600">Showing ${start} to ${end} of ${total} appointments</p>`
                    );
                    const $right = $('<div class="flex flex-wrap gap-2 justify-center"></div>');

                    const makeBtn = (label, page, isActive) => {
                        const cls = isActive ? 'px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg text-xs sm:text-sm' :
                            'px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-xs sm:text-sm';
                        const $b = $(`<button class="${cls}">${label}</button>`);
                        $b.on('click', function() {
                            loadAppointments(page);
                        });
                        return $b;
                    };

                    $right.append(makeBtn('Previous', Math.max(1, current - 1), false));

                    const delta = 2;
                    let startPage = Math.max(1, current - delta);
                    let endPage = Math.min(last, current + delta);
                    if (current <= delta) endPage = Math.min(last, 1 + 2 * delta);
                    if (current + delta >= last) startPage = Math.max(1, last - 2 * delta);

                    for (let p = startPage; p <= endPage; p++) {
                        $right.append(makeBtn(p, p, p === current));
                    }

                    $right.append(makeBtn('Next', Math.min(last, current + 1), false));

                    $wrap.append($left).append($right);
                    $pagination.html($wrap);
                }

                function loadAppointments(page = 1) {
                    const params = {
                        date: $('#filterDate').val(),
                        status: $('#filterStatus').val(),
                        search: $('#filterSearch').val(),
                        page: page
                    };

                    $container.html('<div class="p-6 text-center text-sm text-gray-500">Loading appointments…</div>');

                    $.get("{{ route('doctor.appointments.data') }}", params).done(function(res) {
                        const items = res.data || [];
                        $container.empty();

                        if (!items.length) {
                            $container.append(
                                '<div class="p-6 text-center text-sm text-gray-500">No appointments found.</div>'
                            );
                            renderPagination(res);
                            return;
                        }

                        if (params.date) {
                            const d = params.date.split('-');
                            if (d.length === 3) $headerDate.text(`${d[2]}-${d[1]}-${d[0]}`);
                        } else if (items[0].date) {
                            $headerDate.text(items[0].date);
                        }

                        items.forEach(function(a) {
                            $container.append(renderCard(a));
                        });
                        renderPagination(res);
                    }).fail(function() {
                        $container.html(
                            '<div class="p-6 text-center text-sm text-red-500">Failed to load appointments.</div>'
                        );

                        if (window.toastr && typeof window.toastr.error === 'function') {
                            window.toastr.error('Failed to load appointments.');
                        }
                    });
                }
                loadAppointments();

                $('#filterBtn').on('click', function() {
                    loadAppointments(1);
                });

                // Mark completed -> open modal confirmation
                let __appointmentToComplete = null;
                const $confirmModal = $('#confirmCompleteModal');
                const $confirmBtn = $('#confirmCompleteBtn');
                const $cancelBtn = $('#cancelCompleteBtn');

                function showConfirmModal(id) {
                    __appointmentToComplete = id;
                    $confirmModal.removeClass('hidden').addClass('flex');
                }

                function hideConfirmModal() {
                    __appointmentToComplete = null;
                    $confirmModal.removeClass('flex').addClass('hidden');
                    $confirmBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                }

                $container.on('click', '.btn-completed', function() {
                    const id = $(this).data('id');
                    showConfirmModal(id);
                });

                $cancelBtn.on('click', function() {
                    hideConfirmModal();
                });

                $confirmBtn.on('click', function() {
                    if (!__appointmentToComplete) return;
                    let id = __appointmentToComplete;
                    $confirmBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');

                    $.ajax({
                        url: `/doctor/appointments/${id}/complete`,
                        type: "POST",
                        data: {},
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            try {
                                hideConfirmModal();

                                // SUCCESS
                                if (response.status == 200 || response.success || response.msg) {
                                    toastr.success(response.msg ?? "Appointment completed", "", {
                                        closeButton: false
                                    });

                                    // Reload appointments
                                    loadAppointments();

                                } else {
                                    toastr.error(response.msg || "Something went wrong.", "", {
                                        closeButton: false
                                    });
                                }

                            } catch (e) {
                                toastr.error("An error occurred while processing the response.", "", {
                                    closeButton: false
                                });
                                console.error(e);
                            }
                            $confirmBtn.prop('disabled', false).removeClass(
                                'opacity-50 cursor-not-allowed');
                        },

                        error: function(xhr, status, error) {
                            hideConfirmModal();

                            try {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    Object.keys(errors).forEach(function(key) {
                                        toastr.error(errors[key][0], "", {
                                            closeButton: false
                                        });
                                    });
                                } else {
                                    toastr.error(
                                        (xhr.responseJSON && (xhr.responseJSON.msg || xhr
                                            .responseJSON.message)) ||
                                        ("Error: " + error),
                                        "", {
                                            closeButton: false
                                        }
                                    );
                                }
                            } catch (e) {
                                toastr.error("A server error occurred.", "", {
                                    closeButton: false
                                });
                                console.error(e);
                            }
                            $confirmBtn.prop('disabled', false)
                                .removeClass('opacity-50 cursor-not-allowed');
                        }
                    });
                });
            })();
        </script>
    @endpush

@endsection
