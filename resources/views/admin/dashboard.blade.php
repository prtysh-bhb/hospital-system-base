@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Total Patients</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2"><span id="totalPatientsCount"></span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            {{-- <p class="text-sm text-green-600 mt-4">↑ 12% from last month</p> --}}
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Today's Appointments</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2"><span id="todaysAppointmentsCount"></span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            {{-- <p class="text-sm text-gray-600 mt-4">8 pending confirmation</p> --}}
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Total Doctors</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2"><span id="totalDoctorsCount"></span></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            {{-- <p class="text-sm text-green-600 mt-4">24 active today</p> --}}
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">Revenue (Today)</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2"><span id="revenueToday"></span></p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h3m5 0h-5m5 3h-2m-6.003 0H14m-3-3c1 0 3 .6 3 3m-1 7-5.003-4H11c1 0 3-.6 3-3"></path></g></svg>
                </div>
            </div>
            {{-- <p class="text-sm text-green-600 mt-4">↑ 8% from yesterday</p> --}}
        </div>
    </div>

    <!-- Recent Appointments & Active Doctors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Recent Appointments -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Recent Appointments</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div id="recentAppointmentsContainer" class="space-y-4 max-h-64 overflow-y-auto">

                </div>
            </div>
        </div>

        <!-- Active Doctors -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Active Doctors Today</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div id="activeDoctorsContainer" class="space-y-4 max-h-64 overflow-y-auto">

                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Recent Activity</h3>
            <span class="text-xs text-gray-400 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Live updates
            </span>
        </div>
        <div class="p-4 sm:p-6">
            <div id="recentActivityContainer" class="space-y-3 max-h-80 overflow-y-auto">
                <!-- Loading State -->
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                    <p class="text-sm text-gray-500 mt-2">Loading recent activity...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            // Load dashboard data and replace the sample items with dynamic content.
            function renderRecentAppointments(list) {
                if (!Array.isArray(list) || list.length === 0) {
                    $('#recentAppointmentsContainer').html(
                        '<p class="text-sm text-gray-500 text-center py-4">No recent appointments</p>');
                    return;
                }
                var html = '';
                list.forEach(function(a, idx) {
                    // create initials for avatar when no image available
                    var initials = (a.patient_name || '—').split(' ').map(function(s) {
                        return s.charAt(0)
                    }).slice(0, 2).join('').toUpperCase();
                    var colorClass = 'bg-sky-100 text-sky-600';
                    var item = '' +
                        '<div class="flex items-center justify-between ' + (idx < list.length - 1 ?
                            'pb-4 border-b border-gray-100' : '') + '">' +
                        '<div class="flex items-center">' +
                        '<div class="w-10 h-10 ' + colorClass +
                        ' rounded-full flex items-center justify-center font-semibold">' + initials + '</div>' +
                        '<div class="ml-3">' +
                        '<p class="text-sm font-medium text-gray-800">' + (a.patient_name || '—') + '</p>' +
                        '<p class="text-xs text-gray-500">' + (a.doctor_name ? a.doctor_name + ' • ' : '') + (a
                            .date || '') + (a.time ? ' ' + a.time : '') + '</p>' +
                        '</div></div>' +
                        '<span class="px-3 py-1 text-xs font-medium ' + (a.status_color ||
                            'text-gray-700 bg-gray-100') + ' rounded-full">' + (a.status_label || '') +
                        '</span>' +
                        '</div>';
                    html += item;
                });
                $('#recentAppointmentsContainer').html(html);
            }

            function renderActiveDoctors(list) {
                if (!Array.isArray(list) || list.length === 0) {
                    $('#activeDoctorsContainer').html(
                        '<p class="text-sm text-gray-500 text-center py-4">No active doctors today</p>');
                    return;
                }
                var html = '';
                list.forEach(function(d, idx) {
                    var avatar = d.avatar || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(d.name ||
                        '') + '&background=8b5cf6&color=fff');
                    var item = '' +
                        '<div class="flex items-center justify-between ' + (idx < list.length - 1 ?
                            'pb-4 border-b border-gray-100' : '') + '">' +
                        '<div class="flex items-center">' +
                        '<img src="' + avatar + '" class="w-12 h-12 rounded-full" alt="Doctor">' +
                        '<div class="ml-3">' +
                        '<p class="text-sm font-medium text-gray-800">' + (d.name || '—') + '</p>' +
                        '<p class="text-xs text-gray-500">' + (d.specialty || '') + '</p>' +
                        '</div></div>' +
                        '<div class="text-right">' +
                        '<p class="text-sm font-semibold text-gray-800">' + (d.totalAppointments || 0) +
                        ' patients</p>' +
                        '<p class="text-xs text-gray-500">' + (d.timing || '') + '</p>' +
                        '</div>' +
                        '</div>';
                    html += item;
                });
                $('#activeDoctorsContainer').html(html);
            }

            function getActivityIcon(icon, color) {
                var icons = {
                    'plus-circle': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    'pencil': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
                    'trash': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>',
                    'refresh': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>',
                    'login': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>',
                    'logout': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>',
                    'information-circle': '<svg class="w-4 h-4 text-' + color +
                        '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                };
                return icons[icon] || icons['information-circle'];
            }

            function renderRecentActivity(list) {
                if (!Array.isArray(list) || list.length === 0) {
                    $('#recentActivityContainer').html(
                        '<div class="text-center py-8"><svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><p class="text-gray-500 text-sm">No recent activity</p></div>'
                        );
                    return;
                }
                var html = '';
                list.forEach(function(activity) {
                    var icon = activity.icon || 'information-circle';
                    var color = activity.color || 'gray';
                    html += '' +
                        '<div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">' +
                        '<div class="w-8 h-8 bg-' + color +
                        '-100 rounded-full flex items-center justify-center flex-shrink-0">' +
                        getActivityIcon(icon, color) +
                        '</div>' +
                        '<div class="flex-1 min-w-0">' +
                        '<p class="text-sm text-gray-800">' + (activity.description || activity.title || '') +
                        '</p>' +
                        '<div class="flex items-center gap-2 mt-1">' +
                        '<span class="text-xs text-gray-500">' + (activity.time_ago || activity.time || '') +
                        '</span>' +
                        (activity.user_name ? '<span class="text-xs text-gray-400">by ' + activity.user_name +
                            '</span>' : '') +
                        '</div>' +
                        '</div>' +
                        '<span class="px-2 py-0.5 bg-' + color + '-100 text-' + color +
                        '-700 text-xs font-medium rounded capitalize flex-shrink-0">' +
                        (activity.action || '').replace('_', ' ') +
                        '</span>' +
                        '</div>';
                });
                $('#recentActivityContainer').html(html);
            }

            function loadStats() {
                $.ajax({
                    url: "{{ route('admin.dashboard.details') }}",
                    method: 'GET',
                    success: function(res) {
                        // update metric numbers but keep original markup/classes
                        $('#totalPatientsCount').text(res.totalPatients ?? $('#totalPatientsCount').text());
                        $('#todaysAppointmentsCount').text(res.todaysAppointments ?? $(
                            '#todaysAppointmentsCount').text());
                        $('#totalDoctorsCount').text(res.totalDoctors ?? $('#totalDoctorsCount').text());
                        var revenue = (res.revenueToday || 0);
                        $('#revenueToday').text('₹' + Number(revenue).toLocaleString());

                        // render lists (if present)
                        if (res.recentAppointments) renderRecentAppointments(res.recentAppointments);
                        if (res.activeDoctors) renderActiveDoctors(res.activeDoctors);
                        if (res.recentActivity) renderRecentActivity(res.recentActivity);
                    },
                    error: function() {
                        console.error('Could not load dashboard stats');
                    }
                });
            }

            $(document).ready(function() {
                loadStats();
                // refresh every 10 seconds
                setInterval(loadStats, 10000);
            });
        })();
    </script>
@endpush
