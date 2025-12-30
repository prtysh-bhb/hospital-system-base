<div id="cancel-appointment-modal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative">
        <button id="close-cancel-modal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-full p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Cancel Appointment</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to cancel this appointment? This action cannot be undone.
        </p>

        <form id="cancel-appointment-form">
            <input type="hidden" id="cancel-appointment-id" name="appointment_id">
            <div class="mb-4">
                <label for="cancellation-reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Cancellation <span class="text-red-600">*</span>
                </label>
                <textarea id="cancellation-reason" name="cancellation_reason" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
                    placeholder="Please provide a reason for cancelling this appointment (minimum 10 characters)..."></textarea>
                <p id="cancellation-reason-error" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-modal-close-btn"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <button type="submit" id="confirm-cancel-btn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>
