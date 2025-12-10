@extends('layouts.doctor')

@section('title', 'Doctor Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['today_appointments'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500">Today's Appointments</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['completed_today'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500">Completed Today</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['pending_today'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500">Pending Today</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['total_patients'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500">Total Patients</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <h3 class="font-semibold text-gray-800 mb-3 sm:mb-4 text-base sm:text-lg">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('doctor.calendar') }}"
                class="px-3 sm:px-4 py-2 sm:py-3 bg-sky-50 text-sky-700 rounded-lg font-medium hover:bg-sky-100 text-sm sm:text-base text-center">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                View Schedule
            </a>
            <a href="{{ route('doctor.appointments') }}"
                class="px-3 sm:px-4 py-2 sm:py-3 bg-purple-50 text-purple-700 rounded-lg font-medium hover:bg-purple-100 text-sm sm:text-base text-center">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                All Appointments
            </a>

        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 text-base sm:text-lg">Today's Appointments</h3>
            @if ($upcomingCount > 0)
                <span class="text-xs sm:text-sm text-gray-500">{{ $upcomingCount }} upcoming this week</span>
            @endif
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($todayAppointments as $appointment)
                <div class="p-4 sm:p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-medium text-gray-800 text-sm sm:text-base">{{ $appointment['patient_name'] }}</p>
                            <p class="text-xs sm:text-sm text-gray-500">{{ $appointment['patient_age'] }} yrs •
                                {{ $appointment['patient_gender'] }} • {{ $appointment['patient_blood_group'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $appointment['appointment_number'] }}</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="px-2 sm:px-3 py-1 text-xs font-medium 
                            @if ($appointment['status'] == 'pending') text-amber-700 bg-amber-100
                            @elseif($appointment['status'] == 'confirmed') text-sky-700 bg-sky-100
                            @elseif($appointment['status'] == 'checked_in') text-purple-700 bg-purple-100
                            @elseif($appointment['status'] == 'in_progress') text-blue-700 bg-blue-100
                            @elseif($appointment['status'] == 'completed') text-emerald-700 bg-emerald-100
                            @else text-gray-700 bg-gray-100 @endif
                            rounded-full">
                                {{ $appointment['appointment_time'] }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $appointment['appointment_type'] }}</p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mb-3"><strong>Reason:</strong> {{ $appointment['reason'] }}
                    </p>
                    <div class="flex gap-2">
                        @if (in_array($appointment['status'], ['pending', 'confirmed']))
                            <a href="{{ route('doctor.appointments', $appointment['id']) }}"
                                class="flex-1 px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg font-medium text-sm sm:text-base text-center hover:bg-sky-700">
                                Start Consultation
                            </a>
                        @elseif($appointment['status'] == 'in_progress')
                            <a href="{{ route('doctor.appointments', $appointment['id']) }}"
                                class="flex-1 px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg font-medium text-sm sm:text-base text-center hover:bg-purple-700">
                                Continue Consultation
                            </a>
                        @else
                            <a href="{{ route('doctor.appointments', $appointment['id']) }}"
                                class="flex-1 px-3 sm:px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm sm:text-base text-center hover:bg-gray-50">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 text-sm sm:text-base">No appointments scheduled for today</p>
                    <a href="{{ route('doctor.calendar') }}"
                        class="mt-4 inline-block px-4 py-2 bg-sky-600 text-white rounded-lg text-sm hover:bg-sky-700">
                        View Schedule
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
