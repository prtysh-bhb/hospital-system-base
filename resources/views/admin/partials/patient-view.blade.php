@php
    $age = $patient->user->date_of_birth ? \Carbon\Carbon::parse($patient->user->date_of_birth)->age : 'N/A';
    $initials = strtoupper(substr($patient->user->first_name, 0, 1) . substr($patient->user->last_name, 0, 1));
@endphp

<div class="space-y-6">
    <!-- Patient Header -->
    <div class="flex items-center space-x-4 pb-6 border-b border-gray-200">
        <div class="w-20 h-20 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center font-bold text-2xl">
            {{ $initials }}
        </div>
        <div>
            <h4 class="text-2xl font-bold text-gray-800">{{ $patient->user->full_name }}</h4>
            <p class="text-sm text-gray-500">Patient ID: #PT{{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <!-- Patient Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="space-y-4">
            <h5 class="text-lg font-semibold text-gray-800 mb-3">Personal Information</h5>

            <div>
                <label class="text-sm font-medium text-gray-600">Email</label>
                <p class="text-gray-800">{{ $patient->user->email }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Phone</label>
                <p class="text-gray-800">{{ $patient->user->phone ?? 'N/A' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Date of Birth</label>
                <p class="text-gray-800">
                    @if ($patient->user->date_of_birth)
                        {{ \Carbon\Carbon::parse($patient->user->date_of_birth)->format('d M Y') }}
                        <span class="text-sm text-gray-500">({{ $age }} years)</span>
                    @else
                        N/A
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Gender</label>
                <p class="text-gray-800">{{ ucfirst($patient->user->gender ?? 'N/A') }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Blood Group</label>
                <p class="text-gray-800">
                    @if ($patient->blood_group)
                        <span class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                            {{ $patient->blood_group }}
                        </span>
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="space-y-4">
            <h5 class="text-lg font-semibold text-gray-800 mb-3">Additional Information</h5>

            <div>
                <label class="text-sm font-medium text-gray-600">Status</label>
                <p>
                    <span
                        class="px-3 py-1 text-xs font-medium rounded-full
                        @if ($patient->user->status === 'active') bg-green-100 text-green-700
                        @elseif($patient->user->status === 'inactive') bg-gray-100 text-gray-700
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ ucfirst($patient->user->status) }}
                    </span>
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Address</label>
                <p class="text-gray-800">{{ $patient->user->address ?? 'N/A' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Emergency Contact Name</label>
                <p class="text-gray-800">{{ $patient->emergency_contact_name ?? 'N/A' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Emergency Contact Phone</label>
                <p class="text-gray-800">{{ $patient->emergency_contact_phone ?? 'N/A' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Registered On</label>
                <p class="text-gray-800">{{ $patient->created_at->addHours(1.0)->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Medical Information Section -->
    <div class="pt-6 border-t border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800 mb-3">Medical Information</h5>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Medical History</label>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1">
                    <p class="text-sm text-gray-700">{{ $patient->medical_history ?? 'No medical history recorded' }}
                    </p>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Current Medications</label>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1">
                    <p class="text-sm text-gray-700">{{ $patient->current_medications ?? 'No medications recorded' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Insurance Information Section -->
    <div class="pt-6 border-t border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800 mb-3">Insurance Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Insurance Provider</label>
                <p class="text-gray-800">{{ $patient->insurance_provider ?? 'N/A' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Insurance Number</label>
                <p class="text-gray-800">{{ $patient->insurance_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Medical History Section -->
    <div class="pt-6 border-t border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800 mb-3">Medical History</h5>
        @if ($patient->appointments && $patient->appointments->count() > 0)
            <div class="space-y-2">
                <p class="text-sm text-gray-600">Total Appointments: {{ $patient->appointments->count() }}</p>
                <p class="text-sm text-gray-600">Last Visit:
                    @php
                        $lastVisit = $patient->appointments()->latest()->first();
                    @endphp
                    @if ($lastVisit)
                        {{ $lastVisit->appointment_date->format('d M Y') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        @else
            <p class="text-gray-500">No appointment history</p>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <button onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Close
        </button>
        <button onclick="closeModal(); editPatient({{ $patient->id }})"
            class="px-4 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
            Edit Patient
        </button>
    </div>
</div>
