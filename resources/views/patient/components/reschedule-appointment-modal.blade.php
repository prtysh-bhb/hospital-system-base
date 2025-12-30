<div id="reschedule-appointment-modal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative">
        <button id="close-reschedule-modal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-full p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Reschedule Appointment</h3>
        <p class="text-gray-600 mb-4">Select a new date and time for your appointment.</p>

        <form id="reschedule-appointment-form">
            <input type="hidden" id="reschedule-appointment-id" name="appointment_id">
            <input type="hidden" id="reschedule-doctor-id" name="doctor_id">

            <div class="mb-4">
                <label for="new-date" class="block text-sm font-medium text-gray-700 mb-2">
                    New Date <span class="text-red-600">*</span>
                </label>
                <input type="date" id="new-date" name="new_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    min="{{ date('Y-m-d') }}">
                <p id="new-date-error" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="mb-4">
                <label for="new-time" class="block text-sm font-medium text-gray-700 mb-2">
                    New Time <span class="text-red-600">*</span>
                </label>
                <select id="new-time" name="new_time"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
                    <option value="">Select a time slot</option>
                </select>
                <p id="new-time-error" class="text-xs text-red-500 mt-1 hidden"></p>
                <p id="time-slot-loading" class="text-xs text-gray-500 mt-1 hidden">Loading available time slots...</p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="reschedule-modal-close-btn"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <button type="submit" id="confirm-reschedule-btn"
                    class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">
                    Confirm Reschedule
                </button>
            </div>
        </form>
    </div>
</div>
