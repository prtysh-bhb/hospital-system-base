@extends('layouts.public')

@section('title', 'Welcome')
@section('body-class',
    'bg-gradient-to-br from-sky-50 via-white to-sky-50 min-h-screen flex items-center justify-center
    p-4 sm:p-6 lg:p-8')

@section('content')
    <div class="w-full max-w-5xl">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-sky-600 rounded-xl mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2 px-4">Welcome to MediCare HMS
            </h1>
            <p class="text-sm sm:text-base text-gray-600">Hospital Management System</p>
        </div>

        <!-- Role Selection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Public Booking -->
            <a href="{{ route('booking') }}"
                class="group bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-sky-500 hover:shadow-md transition-all">
                <div class="flex items-center space-x-3 mb-3">
                    <div
                        class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Book Appointment</h3>
                        <p class="text-xs text-gray-500">For Patients</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-3">Schedule an appointment with our doctors online.
                    Quick and easy booking process.</p>
                <div class="flex items-center text-sky-600 font-medium group-hover:text-sky-700 text-sm">
                    Start Booking
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <!-- Admin Panel -->
            <a href="{{ route('admin.dashboard') }}"
                class="group bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-sky-500 hover:shadow-md transition-all">
                <div class="flex items-center space-x-3 mb-3">
                    <div
                        class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center group-hover:bg-sky-200 transition-colors flex-shrink-0">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Admin Panel</h3>
                        <p class="text-xs text-gray-500">For Administrators</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-3">Manage appointments, doctors, patients, and
                    hospital operations.</p>
                <div class="flex items-center text-sky-600 font-medium group-hover:text-sky-700 text-sm">
                    Go to Admin Panel
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <!-- Doctor Portal -->
            <a href="{{ route('doctor.dashboard') }}"
                class="group bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-sky-500 hover:shadow-md transition-all">
                <div class="flex items-center space-x-3 mb-3">
                    <div
                        class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Doctor Portal</h3>
                        <p class="text-xs text-gray-500">For Doctors</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-3">View your appointments, patient records, and
                    manage your schedule.</p>
                <div class="flex items-center text-sky-600 font-medium group-hover:text-sky-700 text-sm">
                    Go to Doctor Portal
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <!-- Front Desk -->
            <a href="{{ route('frontdesk.dashboard') }}"
                class="group bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-sky-500 hover:shadow-md transition-all">
                <div class="flex items-center space-x-3 mb-3">
                    <div
                        class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Front Desk</h3>
                        <p class="text-xs text-gray-500">For Reception Staff</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-3">Quick appointment booking, patient check-in, and
                    schedule viewing.</p>
                <div class="flex items-center text-sky-600 font-medium group-hover:text-sky-700 text-sm">
                    Go to Front Desk
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- Staff Login -->
        <div class="text-center space-x-3">
            <a class="inline-flex items-center px-5 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors text-sm"
                href="{{ route('patient.login') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Patient Login</a>
            <a href="{{ route('login') }}"
                class="inline-flex items-center px-5 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Staff Sign In
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-500 mt-6">
            © 2025 MediCare HMS. All rights reserved.
        </p>
    </div>
@endsection
