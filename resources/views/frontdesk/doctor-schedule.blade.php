@extends('layouts.frontdesk')

@section('title', 'Doctor Schedule')

@section('page-title', 'Doctor Schedule')

@section('content')
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" id="schedule_date"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Specialty</label>
                <select id="specialty_filter"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                    <option value="all">All Specialties</option>
                    <!-- Dynamically populated -->
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Availability</label>
                <select id="availability_filter"
                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 text-sm sm:text-base">
                    <option value="all">All Doctors</option>
                    <option value="available">Available</option>
                    <option value="busy">Busy</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadSchedule()"
                    class="w-full px-4 sm:px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm sm:text-base">
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Schedule Overview -->
    <div class="mb-4 sm:mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800" id="schedule-title">Loading schedule...</h3>
    </div>

    <!-- Doctor Schedule Cards -->
    <div id="doctors-container" class="space-y-4 sm:space-y-6">
        <!-- Loading State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <svg class="animate-spin h-8 w-8 text-sky-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="text-gray-500 mt-3">Loading doctor schedules...</p>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <h4 class="text-xs sm:text-sm font-semibold text-gray-800 mb-3">Legend</h4>
        <div class="flex flex-wrap gap-3 sm:gap-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-green-100 border-2 border-green-300 rounded"></div>
                <span class="text-xs sm:text-sm text-gray-600">Available</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-red-100 border-2 border-red-300 rounded"></div>
                <span class="text-xs sm:text-sm text-gray-600">Booked</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-100 border-2 border-blue-300 rounded"></div>
                <span class="text-xs sm:text-sm text-gray-600">Completed</span>
            </div>
        </div>
    </div>

    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('schedule_date').value = formattedDate;
            loadSchedule();
        });

        // Load schedule
        function loadSchedule() {
            const date = document.getElementById('schedule_date').value;
            const specialty = document.getElementById('specialty_filter').value;
            const availability = document.getElementById('availability_filter').value;

            const params = new URLSearchParams({
                date: date,
                specialty: specialty,
                availability: availability
            });

            fetch(`/frontdesk/doctor-schedule?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);

                    // Check if response is JSON
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error(`Expected JSON but got HTML. Please check the server response.`);
                        });
                    }
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        updateTitle(data.date, data.day_name);
                        populateSpecializations(data.specializations);
                        displayDoctors(data.doctors);
                    } else {
                        showError('Failed to load schedule: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Failed to load schedule: ' + error.message);
                });
        }

        // Update title
        function updateTitle(date, dayName) {
            document.getElementById('schedule-title').textContent = `${dayName}'s Schedule - ${date}`;
        }

        // Populate specializations

        function populateSpecializations(specializations) {
            console.log('spacility-------', specializations);
            const select = document.getElementById('specialty_filter');
            const currentValue = select.value;

            // Keep "All Specialties" option and add others
            const options = ['<option value="all">All Specialties</option>'];
            specializations.forEach(spec => {
                options.push(`<option value="${spec}">${spec}</option>`);
            });

            select.innerHTML = options.join('');
            select.value = currentValue; // Restore selected value
        }

        // Display doctors
        function displayDoctors(doctors) {
            const container = document.getElementById('doctors-container');

            if (!doctors || doctors.length === 0) {
                container.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-500 text-lg">No doctors available for the selected criteria</p>
                </div>
            `;
                return;
            }

            container.innerHTML = doctors.map(doctor => {
                const statusColors = {
                    available: 'bg-green-100 text-green-700 border-green-200',
                    busy: 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    unavailable: 'bg-red-100 text-red-700 border-red-200'
                };

                const statusLabels = {
                    available: 'Available',
                    busy: 'Busy',
                    unavailable: 'Unavailable'
                };

                const slotColors = {
                    available: 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100 cursor-pointer',
                    booked: 'border-red-300 bg-red-50 text-red-700',
                    completed: 'border-blue-300 bg-blue-50 text-blue-700'
                };

                const slotLabels = {
                    available: 'Available',
                    booked: 'Booked',
                    completed: 'Completed'
                };

                // Generate time slots HTML
                const slotsHTML = doctor.slots ? doctor.slots.map(slot => {
                    return `
                    <div class="p-2 sm:p-3 border-2 ${slotColors[slot.status] || 'border-gray-300 bg-gray-50 text-gray-700'} rounded-lg text-center">
                        <p class="text-xs sm:text-sm font-medium">${slot.time || 'N/A'}</p>
                        <p class="text-xs mt-0.5 sm:mt-1">${slotLabels[slot.status] || 'Unknown'}</p>
                    </div>
                `;
                }).join('') : '<p class="text-gray-500 col-span-full text-center py-4">No slots available</p>';

                return `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-lg sm:text-2xl">
                                    ${getInitials(doctor.name || 'Unknown')}
                                </div>
                                <div>
                                    <h4 class="text-base sm:text-xl font-semibold text-gray-800">${doctor.name || 'Unknown Doctor'}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500">${doctor.specialization || 'General'} • ${doctor.experience || 0} years experience</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-1 ${statusColors[doctor.availability_status] || 'bg-gray-100 text-gray-700'} rounded-full border">
                                            ${statusLabels[doctor.availability_status] || 'Unknown'}
                                        </span>
                                        <span class="text-xs text-gray-500">Room ${doctor.room || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:text-right">
                                <p class="text-xs sm:text-sm text-gray-500">Working Hours</p>
                                <p class="text-base sm:text-lg font-semibold text-gray-800">${doctor.start_time || '09:00 AM'} - ${doctor.end_time || '05:00 PM'}</p>
                                <p class="text-xs sm:text-sm text-sky-600 mt-1">${doctor.available_slots || 0} / ${doctor.total_slots || 0} slots available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots -->
                    <div class="p-3 sm:p-6 overflow-x-auto">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
                            ${slotsHTML}
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        // Helper function to get initials
        function getInitials(name) {
            if (!name || name === 'Unknown') return 'DR';

            const parts = name.split(' ');
            if (parts.length >= 2) {
                return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }

        // Show error message
        function showError(message) {
            const container = document.getElementById('doctors-container');
            container.innerHTML = `
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-500 text-lg mb-2">Error Loading Schedule</p>
                <p class="text-gray-600">${message}</p>
                <button onclick="loadSchedule()" class="mt-4 px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm">
                    Try Again
                </button>
            </div>
        `;
        }
    </script>
@endsection
