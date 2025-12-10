<div class="space-y-6">
    <!-- Appointment Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Appointment ID</label>
            <p class="text-sm sm:text-base font-semibold text-gray-800">{{ $appointment->appointment_number }}</p>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Status</label>
            <p class="text-sm sm:text-base">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-green-100 text-green-700',
                        'checked_in' => 'bg-indigo-100 text-indigo-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        'no_show' => 'bg-gray-200 text-gray-700',
                    ];
                    $statusColor = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <span class="px-3 py-1 text-xs font-medium {{ $statusColor }} rounded-full">
                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                </span>
            </p>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="border-t pt-4">
        <h4 class="font-semibold text-gray-700 mb-3">Patient Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Patient Name</label>
                <p class="text-sm sm:text-base text-gray-800">{{ $appointment->patient->first_name }}
                    {{ $appointment->patient->last_name }}</p>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Phone</label>
                <p class="text-sm sm:text-base text-gray-800">{{ $appointment->patient->phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Doctor Information -->
    <div class="border-t pt-4">
        <h4 class="font-semibold text-gray-700 mb-3">Doctor Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Doctor Name</label>
                <p class="text-sm sm:text-base text-gray-800">Dr. {{ $appointment->doctor->first_name }}
                    {{ $appointment->doctor->last_name }}</p>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Specialty</label>
                <p class="text-sm sm:text-base text-gray-800">
                    {{ $appointment->doctor->doctorProfile->specialty->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="border-t pt-4">
        <h4 class="font-semibold text-gray-700 mb-3">Appointment Details</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Date</label>
                <p class="text-sm sm:text-base text-gray-800">{{ $appointment->appointment_date->format('d M Y') }}</p>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Time</label>
                <p class="text-sm sm:text-base text-gray-800">{{ substr($appointment->appointment_time, 0, 5) }}</p>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-1">Type</label>
                <p class="text-sm sm:text-base">
                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Reason for Visit -->
    <div class="border-t pt-4">
        <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-2">Reason for Visit</label>
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-700">{{ $appointment->reason_for_visit }}</p>
        </div>
    </div>

    <!-- Notes -->
    @if ($appointment->notes)
        <div class="border-t pt-4">
            <label class="block text-xs sm:text-sm font-medium text-gray-600 mb-2">Additional Notes</label>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-700">{{ $appointment->notes }}</p>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <button type="button" onclick="closeEditModal()"
            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Close
        </button>
        <button type="button" onclick="editAppointmentFromView({{ $appointment->id }})"
            class="px-6 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
            Edit Appointment
        </button>
    </div>
</div>
