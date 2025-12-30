<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        /* Custom styles to match the design */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 2.5rem;
            padding: 0.5rem 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #6b7280;
            line-height: 1.5rem;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6b7280;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Hide clear button for patient select */
        #patient-select+.select2-container .select2-selection__clear {
            display: none;
        }
    </style>
</head>

<body class="p-4 md:p-8">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 sm:mb-8">
        <div class="mb-4 sm:mb-0">
            <h4 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mb-2">
                Welcome, {{ auth()->user()->full_name }} 👋
            </h4>
            <p class="text-gray-600 text-sm sm:text-base">
                Manage your appointments and medical schedule from your dashboard.
            </p>
        </div>

        <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </button>
    </div>

    <form id="logout-form" action="{{ route('patient.logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div
            class="bg-gradient-to-r from-blue-50 to-white rounded-xl shadow-sm p-5 border border-blue-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats->total }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-sky-50 to-white rounded-xl shadow-sm p-5 border border-sky-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-sky-100">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Upcoming</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats->upcoming }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-green-50 to-white rounded-xl shadow-sm p-5 border border-green-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats->today }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-emerald-50 to-white rounded-xl shadow-sm p-5 border border-emerald-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats->completed }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl shadow-sm p-6 border border-sky-100">
            <div class="flex items-start">
                <div class="p-3 rounded-lg bg-white shadow-sm">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Need to see a doctor?</h3>
                    <p class="text-gray-600 text-sm mb-4">Schedule a new appointment with any of our specialists</p>
                    <a
                        class="appointment-booking inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition cursor-pointer">
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-start">
                <div class="p-3 rounded-lg bg-white shadow-sm">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Medical History</h3>
                    <p class="text-gray-600 text-sm mb-4">Access your past appointments and treatment records</p>
                    <button id="medical-history-button"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        View History
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Appointments List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Your Appointments</h3>
                    <p class="text-sm text-gray-600 mt-1">View and manage all your medical appointments</p>
                </div>
                <a
                    class="appointment-booking mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-200 text-sm cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Appointment
                </a>
            </div>
        </div>

        @if ($appointments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
                @foreach ($appointments as $index => $appointment)
                    @php
                        $statusColors = [
                            'Completed' => 'bg-green-100 text-green-700',
                            'Confirmed' => 'bg-blue-100 text-blue-700',
                            'Pending' => 'bg-yellow-100 text-yellow-700',
                            'Cancelled' => 'bg-red-100 text-red-700',
                            'No Show' => 'bg-gray-100 text-gray-700',
                        ];
                        $dotColors = [
                            'Completed' => 'bg-green-500',
                            'Confirmed' => 'bg-blue-500',
                            'Pending' => 'bg-yellow-500',
                            'Cancelled' => 'bg-red-500',
                            'No Show' => 'bg-gray-500',
                        ];
                    @endphp
                    <div class="appointment-card bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:border-sky-200 transition-all duration-300"
                        data-index="{{ $index }}">
                        <div class="p-6">
                            {{-- Doctor Info Section --}}
                            <div class="flex items-start mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-sky-50 flex items-center justify-center flex-shrink-0 ring-2 ring-blue-50">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>

                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 mb-0.5">
                                        {{ $appointment->doctor_name }}
                                    </h4>
                                    <p class="text-sm text-sky-600 font-medium mb-0.5">
                                        {{ $appointment->specialty }}
                                    </p>
                                    @if ($appointment->qualification)
                                        <p class="text-xs text-gray-500">{{ $appointment->qualification }}</p>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-700' }} ml-2">
                                    <span
                                        class="w-2 h-2 rounded-full mr-2 {{ $dotColors[$appointment->status] ?? 'bg-gray-500' }}"></span>
                                    {{ $appointment->status }}
                                </span>
                            </div>

                            {{-- Appointment Details --}}
                            <div class="space-y-2.5 mb-4">
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v18h14V3H5zm4 14H7v-2h2v2zm0-4H7v-2h2v2zm4 4h-2v-2h2v2zm0-4h-2v-2h2v2z" />
                                    </svg>
                                    <span class="font-medium">{{ $appointment->appointment_number ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">
                                        {{ $appointment->date }}
                                    </span>
                                </div>

                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{ $appointment->time }}</span>
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span class="text-gray-500">{{ $appointment->duration }} mins</span>
                                </div>

                                @if ($appointment->appointment_type)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $appointment->appointment_type }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Actions --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-3">
                                    @if ($appointment->has_prescription)
                                        <span
                                            class="inline-flex items-center text-green-600 bg-green-50 px-2.5 py-1.5 rounded-lg"
                                            title="Prescription Available">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs font-medium">Prescription</span>
                                        </span>
                                    @endif
                                </div>

                                <button
                                    class="view-appointment-details inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                    data-index="{{ $index }}"
                                    onclick="event.stopPropagation(); /* Your view details function here */">
                                    View Details
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No appointments yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by booking your first appointment.</p>
                <a href="{{ route('booking') }}"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">
                    Book Appointment
                </a>
            </div>
        @endif
    </div>

    {{-- Include Component Modals --}}
    @include('patient.components.appointment-details-modal')
    @include('patient.components.appointment-booking-modal')
    @include('patient.components.cancel-appointment-modal')
    @include('patient.components.reschedule-appointment-modal')
    @include('patient.components.medical-history-section')

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    @include('patient.components.dashboard-scripts')

</body>

</html>
