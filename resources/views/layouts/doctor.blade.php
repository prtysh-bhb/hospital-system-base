<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.ico') }}">
    <title>@yield('title', 'Doctor Portal') - MediCare HMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:block fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div class="p-4 sm:p-5 border-b flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-sky-700">MediCare HMS</h1>
            </div>
        </div>
        <nav class="p-3 sm:p-4 overflow-y-auto" style="max-height: calc(100vh - 100px);">
            <a href="{{ route('doctor.dashboard') }}"
                class="flex items-center px-3 sm:px-4 py-2 sm:py-3 mb-2 {{ request()->routeIs('doctor.dashboard') ? 'text-white bg-sky-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg text-sm sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('doctor.appointments') }}"
                class="flex items-center px-3 sm:px-4 py-2 sm:py-3 mb-2
                {{ request()->routeIs('doctor.appointment*') ? 'text-white bg-sky-600' : 'text-gray-700 hover:bg-gray-100' }}
                rounded-lg text-sm sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="ml-2">My Appointments</span>
            </a>
            <a href="{{ route('doctor.calendar') }}"
                class="flex items-center px-3 sm:px-4 py-2 sm:py-3 mb-2 {{ request()->routeIs('doctor.calendar') ? 'text-white bg-sky-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg text-sm sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-2">My Schedule</span>
            </a>

             <a href="{{ route('doctor.leaves') }}"
                class="flex items-center px-3 sm:px-4 py-2 sm:py-3 mb-2 {{ request()->routeIs('doctor.leaves') ? 'text-white bg-sky-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg text-sm sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-6a2 2 0 012-2h6M9 7V4a2 2 0 012-2h6m-7 9h7m-7 4h7m-7 4h7M5 3v18a2 2 0 002 2h3m10-22h3a2 2 0 012 2v18a2 2 0 01-2 2h-3" />
                </svg>
                <span class="ml-2">My Leaves</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-8">
                @csrf
                <button type="submit"
                    class="w-full flex items-center px-3 sm:px-4 py-2 sm:py-3 mb-2 text-red-600 hover:bg-red-50 rounded-lg text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="ml-2">Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="min-h-screen lg:ml-64 pb-20 lg:pb-0">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <h1 class="text-lg lg:hidden font-bold text-sky-700">MediCare</h1>
                    @yield('header-back-button')
                    <h2 class="hidden lg:block text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800">
                        @yield('page-title')</h2>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @yield('header-actions')
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) }}&background=0ea5e9&color=fff"
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full" alt="Admin">
                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->first_name }}
                                {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
        <div class="flex justify-around items-center h-16">
            <!-- Dashboard -->
            <a href="{{ route('doctor.dashboard') }}"
                class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('doctor.dashboard') ? 'text-sky-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs mt-1">Dashboard</span>
            </a>

            <!-- Appointments -->
            <a href="{{ route('doctor.appointments') }}"
                class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('doctor.appointment*') ? 'text-sky-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs mt-1">Appointments</span>
            </a>

            <!-- Schedule -->
            <a href="{{ route('doctor.calendar') }}"
                class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('doctor.calendar') ? 'text-sky-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs mt-1">Schedule</span>
            </a>
        </div>
    </nav>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @stack('scripts')
</body>

</html>
