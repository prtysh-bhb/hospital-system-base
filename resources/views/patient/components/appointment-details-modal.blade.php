<div id="appointment-details-modal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative max-h-[90vh] overflow-y-auto">
        <button id="close-appointment-details"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-full p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Appointment Details</h3>
        <div id="appointment-details-content">
            <!-- Appointment details will be populated here by JavaScript -->
        </div>
    </div>
</div>
