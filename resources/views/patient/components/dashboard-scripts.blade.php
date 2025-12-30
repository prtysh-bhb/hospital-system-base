<script>
    document.addEventListener('DOMContentLoaded', function() {
        const appointments = @json($appointments);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const statusColors = {
            'completed': 'bg-green-100 text-green-700',
            'confirmed': 'bg-blue-100 text-blue-700',
            'pending': 'bg-yellow-100 text-yellow-700',
            'cancelled': 'bg-red-100 text-red-700',
            'no_show': 'bg-gray-100 text-gray-700'
        };

        const dotColors = {
            'completed': 'bg-green-500',
            'confirmed': 'bg-blue-500',
            'pending': 'bg-yellow-500',
            'cancelled': 'bg-red-500',
            'no_show': 'bg-gray-500'
        };

        function generatePrescriptionHTML(prescription) {
            if (!prescription) return '';

            let medicationsHTML = '';
            if (prescription.medications && prescription.medications.length > 0) {
                medicationsHTML = prescription.medications.map(med => `
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="font-medium text-gray-800">${med.name || 'N/A'}</p>
                    <p class="text-sm text-gray-600">${med.dosage || ''} ${med.frequency || ''}</p>
                    ${med.duration ? `<p class="text-xs text-gray-500">Duration: ${med.duration}</p>` : ''}
                </div>
            `).join('');
            } else {
                medicationsHTML = '<p class="text-gray-500 text-sm">No medications recorded</p>';
            }

            return `
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Prescription
                    <span class="ml-2 text-xs font-normal text-gray-500">#${prescription.prescription_number || 'N/A'}</span>
                </h4>
                ${prescription.diagnosis ? `
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600">Diagnosis</p>
                    <p class="text-gray-800">${prescription.diagnosis}</p>
                </div>
                ` : ''}
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600 mb-2">Medications</p>
                    <div class="space-y-2">${medicationsHTML}</div>
                </div>
                ${prescription.instructions ? `
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600">Instructions</p>
                    <p class="text-gray-800 text-sm">${prescription.instructions}</p>
                </div>
                ` : ''}
                ${prescription.follow_up_date ? `
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm font-medium text-blue-800">Follow-up Date</p>
                    <p class="text-blue-700">${prescription.follow_up_date}</p>
                </div>
                ` : ''}
                ${prescription.notes ? `
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600">Additional Notes</p>
                    <p class="text-gray-800 text-sm">${prescription.notes}</p>
                </div>
                ` : ''}
            </div>
        `;
        }

        function generateAppointmentDetailsHTML(apt) {
            const normalizedStatus = apt.status.toLowerCase().replace(' ', '_');
            const prescriptionHTML = apt.has_prescription && apt.prescription ?
                generatePrescriptionHTML(apt.prescription) : '';

            return `
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">${apt.doctor_name}</h4>
                        <p class="text-sm text-sky-600 font-medium">${apt.specialty}</p>
                        ${apt.qualification ? `<p class="text-xs text-gray-500">${apt.qualification}</p>` : ''}
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Appointment #</p>
                        <p class="text-sm font-medium text-gray-800">${apt.appointment_number || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${statusColors[normalizedStatus] || 'bg-gray-100 text-gray-700'}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 ${dotColors[normalizedStatus] || 'bg-gray-500'}"></span>
                            ${apt.status}
                        </span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="text-sm font-medium text-gray-800">${apt.date || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Time</p>
                        <p class="text-sm font-medium text-gray-800">${apt.time || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Duration</p>
                        <p class="text-sm font-medium text-gray-800">${apt.duration || 30} minutes</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Consultation Fee</p>
                        <p class="text-sm font-medium text-gray-800">₹${apt.consultation_fee || 'N/A'}</p>
                    </div>
                </div>
                ${apt.appointment_type ? `
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Appointment Type</p>
                    <p class="text-sm font-medium text-gray-800">${apt.appointment_type}</p>
                </div>
                ` : ''}
                ${apt.reason_for_visit ? `
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Reason for Visit</p>
                    <p class="text-sm text-gray-800">${apt.reason_for_visit}</p>
                </div>
                ` : ''}
                ${apt.symptoms ? `
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Symptoms</p>
                    <p class="text-sm text-gray-800">${apt.symptoms}</p>
                </div>
                ` : ''}
                ${apt.notes ? `
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Notes</p>
                    <p class="text-sm text-gray-800">${apt.notes}</p>
                </div>
                ` : ''}
                ${apt.cancellation_reason ? `
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-xs text-red-600 mb-1">Cancellation Reason</p>
                    <p class="text-sm text-red-800">${apt.cancellation_reason}</p>
                </div>
                ` : ''}
                ${prescriptionHTML}
                <div class="pt-4 border-t border-gray-100">
                    ${apt.has_prescription && apt.prescription ? `
                    <button onclick="downloadPrescription(${apt.id}, ${apt.prescription.id})" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-200 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Prescription
                    </button>
                    ` : ''}
                    ${apt.status === 'pending' || apt.status === 'confirmed' ? `
                    <button onclick="showRescheduleModal(${apt.id}, ${apt.doctor_id})" class="w-full bg-sky-600 text-white py-2 rounded-lg hover:bg-sky-700 transition duration-200 mb-2">
                        Reschedule Appointment
                    </button>
                    <button onclick="showCancelModal(${apt.id})" class="w-full border border-red-300 text-red-600 py-2 rounded-lg hover:bg-red-50 transition duration-200">
                        Cancel Appointment
                    </button>
                    ` : ''}
                </div>
            </div>
        `;
        }

        // Appointment Details Modal Functions
        document.querySelectorAll('.appointment-card').forEach((card, index) => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('.view-appointment-details')) return;
                const appointment = appointments[index];
                const modal = document.getElementById('appointment-details-modal');
                const content = document.getElementById('appointment-details-content');
                content.innerHTML = generateAppointmentDetailsHTML(appointment);
                modal.classList.remove('hidden');
            });
        });

        // Add separate handler for View Details buttons
        document.querySelectorAll('.view-appointment-details').forEach((button, index) => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const cardIndex = this.closest('.appointment-card').dataset.index;
                const appointment = appointments[cardIndex];
                const modal = document.getElementById('appointment-details-modal');
                const content = document.getElementById('appointment-details-content');
                content.innerHTML = generateAppointmentDetailsHTML(appointment);
                modal.classList.remove('hidden');
            });
        });

        document.getElementById('close-appointment-details').addEventListener('click', function() {
            document.getElementById('appointment-details-modal').classList.add('hidden');
        });

        document.getElementById('appointment-details-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                this.classList.add('hidden');
            }
        });

        window.viewHistoryAppointmentDetails = function(appointmentId) {
            const appointment = appointments.find(appt => appt.id == appointmentId);
            if (!appointment) return;

            const modal = document.getElementById('appointment-details-modal');
            const content = document.getElementById('appointment-details-content');
            content.innerHTML = generateAppointmentDetailsHTML(appointment);
            modal.classList.remove('hidden');
        };

        window.downloadPrescription = function(appointmentId, prescriptionId) {
            if (!prescriptionId) {
                toastr.error('Prescription not found');
                return;
            }
            const downloadUrl = `{{ route('patient.prescription.download', ['id' => '__ID__']) }}`.replace(
                '__ID__', prescriptionId);
            window.open(downloadUrl, '_blank');
        };

        // Cancel Appointment Functions
        window.showCancelModal = function(appointmentId) {
            document.getElementById('cancel-appointment-id').value = appointmentId;
            document.getElementById('cancellation-reason').value = '';
            document.getElementById('cancellation-reason-error').classList.add('hidden');
            document.getElementById('cancel-appointment-modal').classList.remove('hidden');
            document.getElementById('appointment-details-modal').classList.add('hidden');
        };

        document.getElementById('close-cancel-modal').addEventListener('click', function() {
            document.getElementById('cancel-appointment-modal').classList.add('hidden');
        });

        document.getElementById('cancel-modal-close-btn').addEventListener('click', function() {
            document.getElementById('cancel-appointment-modal').classList.add('hidden');
        });

        document.getElementById('cancel-appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const appointmentId = document.getElementById('cancel-appointment-id').value;
            const cancellationReasonField = document.getElementById('cancellation-reason');
            const cancellationReason = cancellationReasonField.value.trim();
            const errorElement = document.getElementById('cancellation-reason-error');
            const submitBtn = document.getElementById('confirm-cancel-btn');

            cancellationReasonField.classList.remove('border-red-500');
            errorElement.classList.add('hidden');
            errorElement.textContent = '';

            let isValid = true;

            if (!cancellationReason) {
                cancellationReasonField.classList.add('border-red-500');
                errorElement.textContent = 'Please provide a reason for cancellation.';
                errorElement.classList.remove('hidden');
                isValid = false;
            } else if (cancellationReason.length < 10) {
                cancellationReasonField.classList.add('border-red-500');
                errorElement.textContent = 'Cancellation reason must be at least 10 characters.';
                errorElement.classList.remove('hidden');
                isValid = false;
            } else if (cancellationReason.length > 500) {
                cancellationReasonField.classList.add('border-red-500');
                errorElement.textContent = 'Cancellation reason cannot exceed 500 characters.';
                errorElement.classList.remove('hidden');
                isValid = false;
            }

            if (!isValid) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            fetch('{{ route('patient.cancel-appointment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        appointment_id: appointmentId,
                        cancellation_reason: cancellationReason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        document.getElementById('cancel-appointment-modal').classList.add('hidden');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        toastr.error(data.message || 'Failed to cancel appointment.');
                        if (data.errors && data.errors.cancellation_reason) {
                            cancellationReasonField.classList.add('border-red-500');
                            errorElement.textContent = data.errors.cancellation_reason[0];
                            errorElement.classList.remove('hidden');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Confirm Cancellation';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while cancelling the appointment.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Confirm Cancellation';
                });
        });

        document.getElementById('cancellation-reason').addEventListener('input', function() {
            this.classList.remove('border-red-500');
            document.getElementById('cancellation-reason-error').classList.add('hidden');
        });

        // Reschedule Appointment Functions
        window.showRescheduleModal = function(appointmentId, doctorId) {
            document.getElementById('reschedule-appointment-id').value = appointmentId;
            document.getElementById('reschedule-doctor-id').value = doctorId;
            document.getElementById('new-date').value = '';
            document.getElementById('new-time').innerHTML = '<option value="">Select a time slot</option>';
            document.getElementById('new-date-error').classList.add('hidden');
            document.getElementById('new-time-error').classList.add('hidden');
            document.getElementById('reschedule-appointment-modal').classList.remove('hidden');
            document.getElementById('appointment-details-modal').classList.add('hidden');
        };

        document.getElementById('close-reschedule-modal').addEventListener('click', function() {
            document.getElementById('reschedule-appointment-modal').classList.add('hidden');
        });

        document.getElementById('reschedule-modal-close-btn').addEventListener('click', function() {
            document.getElementById('reschedule-appointment-modal').classList.add('hidden');
        });

        // Load time slots when date changes
        document.getElementById('new-date').addEventListener('change', function() {
            const date = this.value;
            const doctorId = document.getElementById('reschedule-doctor-id').value;
            const appointmentId = document.getElementById('reschedule-appointment-id').value;
            const timeSelect = document.getElementById('new-time');
            const loadingMsg = document.getElementById('time-slot-loading');

            if (!date) return;

            timeSelect.innerHTML = '<option value="">Loading...</option>';
            timeSelect.disabled = true;
            loadingMsg.classList.remove('hidden');

            fetch(
                    `{{ route('patient.available-time-slots') }}?doctor_id=${doctorId}&date=${date}&appointment_id=${appointmentId}`
                )
                .then(response => response.json())
                .then(data => {
                    loadingMsg.classList.add('hidden');
                    timeSelect.disabled = false;

                    if (data.success && data.slots && data.slots.length > 0) {
                        timeSelect.innerHTML = '<option value="">Select a time slot</option>' +
                            data.slots.map(slot =>
                                `<option value="${slot.time}">${slot.formatted_time}</option>`)
                            .join('');
                    } else {
                        timeSelect.innerHTML = '<option value="">No available slots</option>';
                        toastr.warning('No available time slots for this date.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingMsg.classList.add('hidden');
                    timeSelect.disabled = false;
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    toastr.error('Failed to load time slots.');
                });
        });

        document.getElementById('reschedule-appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const appointmentId = document.getElementById('reschedule-appointment-id').value;
            const newDateField = document.getElementById('new-date');
            const newTimeField = document.getElementById('new-time');

            const newDate = newDateField.value.trim();
            const newTime = newTimeField.value.trim();

            const dateError = document.getElementById('new-date-error');
            const timeError = document.getElementById('new-time-error');
            const submitBtn = document.getElementById('confirm-reschedule-btn');

            newDateField.classList.remove('border-red-500');
            newTimeField.classList.remove('border-red-500');
            dateError.classList.add('hidden');
            timeError.classList.add('hidden');

            let isValid = true;

            if (!newDate) {
                newDateField.classList.add('border-red-500');
                dateError.textContent = 'Please select a new date.';
                dateError.classList.remove('hidden');
                isValid = false;
            }

            if (!newTime) {
                newTimeField.classList.add('border-red-500');
                timeError.textContent = 'Please select a new time.';
                timeError.classList.remove('hidden');
                isValid = false;
            }

            if (!isValid) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            fetch('{{ route('patient.reschedule-appointment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        appointment_id: appointmentId,
                        new_date: newDate,
                        new_time: newTime
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        document.getElementById('reschedule-appointment-modal').classList.add(
                            'hidden');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        toastr.error(data.message || 'Failed to reschedule appointment.');
                        if (data.errors) {
                            if (data.errors.new_date) {
                                newDateField.classList.add('border-red-500');
                                dateError.textContent = data.errors.new_date[0];
                                dateError.classList.remove('hidden');
                            }
                            if (data.errors.new_time) {
                                newTimeField.classList.add('border-red-500');
                                timeError.textContent = data.errors.new_time[0];
                                timeError.classList.remove('hidden');
                            }
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Confirm Reschedule';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while rescheduling the appointment.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Confirm Reschedule';
                });
        });

        document.getElementById('new-date').addEventListener('input', function() {
            this.classList.remove('border-red-500');
            document.getElementById('new-date-error').classList.add('hidden');
        });

        document.getElementById('new-time').addEventListener('change', function() {
            this.classList.remove('border-red-500');
            document.getElementById('new-time-error').classList.add('hidden');
        });

        // Medical History Functions
        const medicalHistoryButton = document.getElementById('medical-history-button');
        const medicalHistorySection = document.getElementById('medical-history-section');
        const closeHistoryBtn = document.getElementById('close-history-btn');

        medicalHistoryButton.addEventListener('click', function() {
            loadMedicalHistory();
            medicalHistorySection.classList.remove('hidden');
            medicalHistorySection.scrollIntoView({
                behavior: 'smooth'
            });
        });

        closeHistoryBtn.addEventListener('click', function() {
            medicalHistorySection.classList.add('hidden');
        });

        function loadMedicalHistory() {
            const historyTableBody = document.getElementById('history-table-body');
            const noHistoryMessage = document.getElementById('no-history-message');

            historyTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>';
            noHistoryMessage.classList.add('hidden');

            fetch('{{ route('patient.medical-history') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.histories && data.histories.length > 0) {
                        let tableHTML = '';
                        data.histories.forEach(history => {
                            tableHTML += `
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${history.date}</div>
                                    <div class="text-sm text-gray-500">${history.time}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${history.doctor_name}</div>
                                    ${history.qualification ? `<div class="text-xs text-gray-500">${history.qualification}</div>` : ''}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">${history.specialty}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${statusColors[history.status] || 'bg-gray-100 text-gray-700'}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 ${dotColors[history.status] || 'bg-gray-500'}"></span>
                                        ${history.status ? history.status.charAt(0).toUpperCase() + history.status.slice(1).replace('_', ' ') : 'N/A'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    ${history.has_prescription ? `
                                    <svg class="w-5 h-5 text-green-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    ` : `
                                    <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    `}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewHistoryAppointmentDetails(${history.id})" 
                                        class="text-sky-600 hover:text-sky-900 transition duration-150">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                        historyTableBody.innerHTML = tableHTML;
                    } else {
                        historyTableBody.innerHTML = '';
                        noHistoryMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    historyTableBody.innerHTML =
                        '<tr><td colspan="6" class="text-center py-4 text-red-600">Error loading history</td></tr>';
                    toastr.error('Failed to load medical history.');
                });
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('appointment-details-modal').classList.add('hidden');
                document.getElementById('cancel-appointment-modal').classList.add('hidden');
                document.getElementById('reschedule-appointment-modal').classList.add('hidden');
                document.getElementById('appointment-booking-modal').classList.add('hidden');
            }
        });

        // Appointment Booking Modal Start
        document.querySelectorAll('.appointment-booking').forEach((button, index) => {
            button.addEventListener('click', function(e) {
                const modal = document.getElementById('appointment-booking-modal').classList
                    .remove('hidden');
            });
        });

        $(document).ready(function() {
            // Initialize character counters
            function updateCharCount(field) {
                const fieldId = field.attr('id');
                const countSpan = $('#' + fieldId + '_count');
                if (countSpan.length) {
                    countSpan.text(field.val().length);
                }
            }

            // Update character counts on input
            $('#reason_for_visit, #notes').on(
                'input',
                function() {
                    updateCharCount($(this));
                });

            // Toggle patient form based on selection
            function togglePatientForm() {
                var pid = $('#select_patient').val();
                if (pid && pid !== '') {
                    $('#patientForm').hide();
                    // Clear any existing errors in patient form
                    $('#patientForm input, #patientForm select, #patientForm textarea').each(
                        function() {
                            clearFieldError($(this));
                        });
                } else {
                    $('#patientForm').show();
                }
            }

            // Initialize form visibility
            togglePatientForm();
            $('#select_patient').on('change', togglePatientForm);

            // Error handling functions
            function showFieldError(field, message) {
                const fieldId = field.attr('id');
                const errorSpanId = fieldId + '_error';
                let errorSpan = $('#' + errorSpanId);

                // If error span doesn't exist, try to find it
                if (!errorSpan.length) {
                    // Check if there's already an error span for this field
                    errorSpan = field.siblings('span[id$="_error"]').first();
                    if (!errorSpan.length) {
                        // Look for error span in the parent container
                        errorSpan = field.closest('div').find('span[id$="_error"]').first();
                    }
                }

                if (errorSpan.length) {
                    errorSpan.text(message).removeClass('hidden');
                    // Add error border to the field
                    field.addClass('border-red-500');

                    // Special handling for Select2
                    if (field.hasClass('select2-hidden-accessible')) {
                        field.next('.select2-container').find('.select2-selection').addClass(
                            'border-red-500');
                    }
                } else {
                    // Fallback: show as toast
                    toastr.error(message);
                }
            }

            function clearFieldError(field) {
                const fieldId = field.attr('id');
                let errorSpan = $('#' + fieldId + '_error');

                if (!errorSpan.length) {
                    // Try to find error span in siblings
                    errorSpan = field.siblings('span[id$="_error"]').first();
                }

                if (errorSpan.length) {
                    errorSpan.addClass('hidden').text('');
                }

                // Remove error border
                field.removeClass('border-red-500');

                // Special handling for Select2
                if (field.hasClass('select2-hidden-accessible')) {
                    field.next('.select2-container').find('.select2-selection').removeClass(
                        'border-red-500');
                }
            }

            // Clear errors on input
            $(document).on('input change', 'input, select, textarea', function() {
                clearFieldError($(this));
            });

            // Clear all errors
            function clearAllErrors() {
                $('input, select, textarea').each(function() {
                    clearFieldError($(this));
                });
            }

            // Function to handle backend errors
            function displayBackendErrors(errors) {
                clearAllErrors();

                Object.keys(errors).forEach(function(key) {
                    // Handle special cases where field names might not match exactly
                    let fieldName = key;
                    let field = $('[name="' + fieldName + '"]');

                    // If not found by name, try by id
                    if (!field.length) {
                        field = $('#' + fieldName);
                    }

                    // If still not found, try common variations
                    if (!field.length) {
                        if (key === 'patient_id') field = $('#select_patient');
                        else if (key === 'doctor_id') field = $('#doctor_select');
                        else if (key === 'appointment_type') field = $('#type_select');
                    }

                    if (field.length) {
                        showFieldError(field, errors[key][0]);
                    } else {
                        // If field not found, show as toast
                        toastr.error(errors[key][0]);
                    }
                });
            }

            // Form submission with AJAX
            $('#appointmentForm').on('submit', function(e) {
                e.preventDefault();
                clearAllErrors();
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Creating...'
                );
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            toastr.success(response.msg, '', {
                                closeButton: false,
                                progressBar: false,
                                timeOut: 2000,
                            });
                            $('#appointment-booking-modal').addClass('hidden');
                            $('#appointmentForm')[0].reset();
                            loadMedicalHistory();
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            // Validation errors from backend
                            let errors = xhr.responseJSON.errors;
                            if (errors) {
                                displayBackendErrors(errors);
                                // toastr.error('Please fix the validation errors.');
                            }
                        } else if (xhr.status === 400) {
                            // Other errors
                            let errorMsg = xhr.responseJSON.msg ||
                                'Something went wrong. Please try again.';
                            // toastr.error(errorMsg);
                        } else {
                            toastr.error(
                                'An unexpected error occurred. Please try again.'
                            );
                        }
                    }
                });
            });

            // Select2 initialization (if needed)
            $('#select_patient, #doctor_select, #type_select').select2({
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select...';
                },
                allowClear: true,
                width: '100%'
            });

            // Load time slots function (from your existing code)
            function loadAvailableSlots() {
                const doctorId = $('#doctor_select').val();
                const date = $('#appointment_date').val();
                const timeSelect = $('#appointment_time');

                if (!doctorId || !date) {
                    timeSelect.html('<option value="">Select doctor and date first</option>');
                    return;
                }

                timeSelect.html('<option value="">Loading slots...</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ route('patient.available-time-slots') }}',
                    method: 'GET',
                    data: {
                        doctor_id: doctorId,
                        date: date
                    },
                    success: function(response) {
                        timeSelect.prop('disabled', false);

                        timeSelect.prop('disabled', false);
                        if (response.success && response.slots && response.slots.length >
                            0) {
                            let options = '<option value="">Select Time</option>';
                            response.slots.forEach(function(slot) {
                                options +=
                                    `<option value="${slot.time}">${slot.formatted_time}</option>`;
                            });
                            timeSelect.html(options);
                        } else {
                            timeSelect.html(
                                '<option value="">No slots available for this date</option>'
                            );
                        }
                    },
                    error: function() {
                        timeSelect.html('<option value="">Error loading slots</option>')
                            .prop(
                                'disabled', false);
                    }
                });
            }

            // Load slots when doctor or date changes
            $('#doctor_select, #appointment_date').on('change', function() {
                clearFieldError($(this));
                loadAvailableSlots();
            });
        });
        // Close modal on clicking the close button
        $('.close-appointment-booking').on('click', function() {
            $('#appointment-booking-modal').addClass('hidden');
        });

        // Close modal when clicking outside the modal content
        $('#appointment-booking-modal').on('click', function(e) {
            // Check if the click is on the overlay (not on the inner modal)
            if ($(e.target).is('#appointment-booking-modal')) {
                $(this).addClass('hidden');
            }
        });

        // Select your date input
        const dateInput = document.getElementById('appointment_date');

        // Disable scroll and arrow key changes
        dateInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault(); // Prevent arrow keys from changing date
            }
        });

        dateInput.addEventListener('wheel', function(e) {
            e.preventDefault(); // Prevent mouse wheel from changing date
        });
        // Appointment Booking Modal End
    });
</script>
