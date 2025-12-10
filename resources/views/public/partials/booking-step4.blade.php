  <!-- Header -->
  <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-sky-600 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                  </div>
                  <div>
                      <h1 class="text-lg sm:text-xl font-bold text-gray-900"><a href="{{ route('home') }}">City General
                              Hospital</a></h1>
                      <p class="text-xs sm:text-sm text-gray-500">Book Your Appointment</p>
                  </div>
              </div>
          </div>
      </div>
  </header>

  <!-- Progress Bar -->
  <form action="{{ route('booking.store') }}" method="POST">
      @csrf
      <input type="hidden" name="step" value="4">
      <div class="bg-white border-b border-gray-200">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-0">
                  <div class="flex items-center flex-1 min-w-0">
                      <div
                          class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-sky-600 text-white font-semibold text-sm sm:text-base flex-shrink-0">
                          ✓
                      </div>
                      <div class="ml-2 sm:ml-3">
                          <p class="text-xs sm:text-sm font-medium text-gray-900">Select Doctor</p>
                      </div>
                  </div>
                  <div class="hidden sm:flex flex-1 h-0.5 bg-sky-600 mx-2 sm:mx-4"></div>

                  <div class="flex items-center flex-1 min-w-0">
                      <div
                          class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-sky-600 text-white font-semibold text-sm sm:text-base flex-shrink-0">
                          ✓
                      </div>
                      <div class="ml-2 sm:ml-3">
                          <p class="text-xs sm:text-sm font-medium text-gray-900">Date & Time</p>
                      </div>
                  </div>
                  <div class="hidden sm:flex flex-1 h-0.5 bg-sky-600 mx-2 sm:mx-4"></div>

                  <div class="flex items-center flex-1 min-w-0">
                      <div
                          class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-sky-600 text-white font-semibold text-sm sm:text-base flex-shrink-0">
                          ✓
                      </div>
                      <div class="ml-2 sm:ml-3">
                          <p class="text-xs sm:text-sm font-medium text-gray-900">Patient Details</p>
                      </div>
                  </div>
                  <div class="hidden sm:flex flex-1 h-0.5 bg-sky-600 mx-2 sm:mx-4"></div>

                  <div class="flex items-center flex-1 min-w-0">
                      <div
                          class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-sky-600 text-white font-semibold text-sm sm:text-base flex-shrink-0">
                          4
                      </div>
                      <div class="ml-2 sm:ml-3">
                          <p class="text-xs sm:text-sm font-medium text-sky-600">Confirmation</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Main Content -->
      <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">

          <!-- Success Message -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4 sm:mb-6">
              <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 sm:p-8 text-center border-b border-gray-200">
                  <div
                      class="w-16 h-16 sm:w-20 sm:h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                      <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                  </div>
                  <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Appointment Confirmed!</h2>
                  <p class="text-xs sm:text-sm text-gray-600">Your appointment has been successfully booked</p>
              </div>

              <!-- Appointment ID -->
              <div class="bg-sky-50 p-4 sm:p-6 border-b border-gray-200">
                  <div class="text-center">
                      <p class="text-xs sm:text-sm text-gray-600 mb-2">Your Appointment ID</p>
                      <div class="text-3xl sm:text-4xl font-bold text-sky-600 tracking-wider mb-2">
                          {{ $appointment->appointment_number }}</div>
                      <p class="text-xs text-gray-500">Please save this ID for your records</p>
                  </div>
              </div>

              <!-- Appointment Details -->
              <div class="p-4 sm:p-8">
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6">Appointment Details</h3>

                  <div class="space-y-3 sm:space-y-4">
                      <!-- Doctor Info -->
                      <div class="flex items-start p-3 sm:p-4 bg-gray-50 rounded-lg">
                          <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->doctor->first_name . ' ' . $appointment->doctor->last_name) }}&background=0ea5e9&color=fff"
                              class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg flex-shrink-0">
                          <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                              <div class="flex items-start justify-between gap-2">
                                  <div class="min-w-0">
                                      <p class="font-semibold text-gray-900 text-sm sm:text-base">Dr.
                                          {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                                      </p>
                                      <p class="text-xs sm:text-sm text-gray-600">
                                          {{ $appointment->doctor->doctorProfile->specialty->name ?? 'N/A' }}</p>
                                      <p class="text-xs text-gray-500 mt-1">
                                          {{ $appointment->doctor->doctorProfile->qualification ?? 'N/A' }} •
                                          {{ $appointment->doctor->doctorProfile->experience_years ?? 0 }} years exp.
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Date & Time -->
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                          <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                              <div class="flex items-center space-x-2 sm:space-x-3">
                                  <div
                                      class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center shrink-0">
                                      <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor"
                                          viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                      </svg>
                                  </div>
                                  <div class="min-w-0">
                                      <p class="text-xs text-gray-500">Date</p>
                                      <p class="font-semibold text-gray-900 text-sm sm:text-base">
                                          {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                      </p>
                                      <p class="text-xs text-gray-600">
                                          {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l') }}</p>
                                  </div>
                              </div>
                          </div>

                          <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                              <div class="flex items-center space-x-2 sm:space-x-3">
                                  <div
                                      class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center shrink-0">
                                      <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor"
                                          viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                      </svg>
                                  </div>
                                  <div class="min-w-0">
                                      <p class="text-xs text-gray-500">Time</p>
                                      <p class="font-semibold text-gray-900 text-sm sm:text-base">
                                          {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                      </p>
                                      <p class="text-xs text-gray-600">{{ $appointment->duration_minutes }} min slot
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Patient Details -->
                      <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                          <p class="text-xs text-gray-500 mb-3">Patient Information</p>
                          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                              <div>
                                  <p class="text-xs text-gray-500">Name</p>
                                  <p class="font-medium text-gray-900 text-sm">{{ $appointment->patient->first_name }}
                                      {{ $appointment->patient->last_name }}</p>
                              </div>
                              <div>
                                  <p class="text-xs text-gray-500">Mobile</p>
                                  <p class="font-medium text-gray-900 text-sm">{{ $appointment->patient->phone }}</p>
                              </div>
                              <div>
                                  <p class="text-xs text-gray-500">Age</p>
                                  <p class="font-medium text-gray-900 text-sm">
                                      {{ $appointment->patient->date_of_birth ? \Carbon\Carbon::parse($appointment->patient->date_of_birth)->age : 'N/A' }}
                                      years</p>
                              </div>
                              <div>
                                  <p class="text-xs text-gray-500">Gender</p>
                                  <p class="font-medium text-gray-900 text-sm">
                                      {{ ucfirst($appointment->patient->gender) }}</p>
                              </div>
                          </div>
                          <div class="mt-3">
                              <p class="text-xs text-gray-500">Reason for Visit</p>
                              <p class="font-medium text-gray-900 text-sm">{{ $appointment->reason_for_visit }}</p>
                          </div>
                      </div>

                      <!-- Appointment Type -->
                      <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                          <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-3">
                              <div>
                                  <p class="text-xs text-gray-500">Appointment Type</p>
                                  <p class="font-semibold text-gray-900 text-sm sm:text-base">
                                      {{ ucfirst($appointment->appointment_type) }}</p>
                              </div>
                              <div class="text-right">
                                  <p class="text-xs text-gray-500">Consultation Fee</p>
                                  <p class="font-semibold text-sky-600 text-base sm:text-lg">
                                      ₹{{ number_format($appointment->doctor->doctorProfile->consultation_fee ?? 0, 2) }}
                                  </p>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Important Instructions -->
              <div class="bg-amber-50 border-t border-amber-100 p-4 sm:p-6">
                  <h4 class="font-semibold text-amber-900 mb-3 flex items-center text-sm sm:text-base">
                      <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd" />
                      </svg>
                      Important Instructions
                  </h4>
                  <ul class="space-y-2 text-xs sm:text-sm text-amber-900">
                      <li class="flex items-start gap-2">
                          <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></span>
                          <span>Please arrive 15 minutes before your scheduled appointment time</span>
                      </li>
                      <li class="flex items-start gap-2">
                          <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></span>
                          <span>Show this Appointment ID at the reception desk</span>
                      </li>
                      <li class="flex items-start gap-2">
                          <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></span>
                          <span>Bring any previous medical records or prescriptions</span>
                      </li>
                      <li class="flex items-start gap-2">
                          <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></span>
                          <span>For cancellation or rescheduling, call: +91 99999 88888</span>
                      </li>
                  </ul>
              </div>
          </div>

          <!-- Action Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
              <button id="downloadPDFAppointment" data-id="{{ $appointment->id }}"
                  data-apt-no="{{ $appointment->appointment_number }}"
                  class="flex
                  items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-white border-2 border-sky-600 text-sky-600
                  font-medium sm:font-semibold rounded-lg hover:bg-sky-50 transition-colors text-sm sm:text-base">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Download PDF
              </button>

              <button
                  class="flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-white border-2 border-sky-600 text-sky-600 font-medium sm:font-semibold rounded-lg hover:bg-sky-50 transition-colors text-sm sm:text-base"
                  onclick="printPage()">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                  Print
              </button>

              <button
                  class="flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-white border-2 border-sky-600 text-sky-600 font-medium sm:font-semibold rounded-lg hover:bg-sky-50 transition-colors text-sm sm:text-base">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  </svg>
                  Share via SMS
              </button>
          </div>

          <!-- Add to Calendar -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
              <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center text-sm sm:text-base">
                  <svg class="w-5 h-5 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Add to Your Calendar
              </h3>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                  <button
                      class="flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs sm:text-sm font-medium">
                      Google Calendar
                  </button>
                  <button
                      class="flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs sm:text-sm font-medium">
                      Apple Calendar
                  </button>
                  <button
                      class="flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs sm:text-sm font-medium">
                      Outlook
                  </button>
                  <button
                      class="flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs sm:text-sm font-medium">
                      Yahoo
                  </button>
              </div>
          </div>

          <!-- Navigation -->
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
              <a href="{{ route('booking', ['step' => 1]) }}"
                  class="w-full sm:w-auto px-6 py-2.5 sm:py-3 bg-gray-100 text-gray-700 font-medium sm:font-semibold rounded-lg hover:bg-gray-200 transition-colors text-sm sm:text-base text-center">
                  Book Another Appointment
              </a>
              <a href="{{ route('home') }}"
                  class="w-full sm:w-auto px-6 py-2.5 sm:py-3 bg-sky-600 text-white font-medium sm:font-semibold rounded-lg hover:bg-sky-700 transition-colors text-sm sm:text-base text-center">
                  Go to Homepage
              </a>
          </div>

      </main>
  </form>

  <!-- Footer -->
  <footer class="bg-white border-t border-gray-200 mt-8 sm:mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
          <div class="text-center text-xs sm:text-sm text-gray-500">
              <p>&copy; 2025 City General Hospital. All rights reserved.</p>
              <p class="mt-1">For support, call: +91 99999 88888 | Email: support@cityhospital.com</p>
          </div>
      </div>
  </footer>

  @push('scripts')
      <script>
          function printPage() {
              window.print();
          }

          $(document).on('click', '#downloadPDFAppointment', function(e) {
              e.preventDefault(); // Prevent default behavior (form submit or link navigation)

              let appointmentId = $(this).data('id');
              let appointmentNo = $(this).data('apt-no');

              $.ajax({
                  url: "/download-appointment?appointment_id=" + appointmentId,
                  type: "GET",
                  xhrFields: {
                      responseType: 'blob' // Important to receive PDF
                  },
                  success: function(data) {
                      const blob = new Blob([data], {
                          type: "application/pdf"
                      });
                      const link = document.createElement('a');
                      link.href = URL.createObjectURL(blob);
                      link.download = "appointment-" + appointmentNo + ".pdf";
                      document.body.appendChild(link);
                      link.click();
                      document.body.removeChild(link);
                  },
                  error: function(xhr) {
                      if (xhr.status === 404) {
                          toastr.error('Appointment not found.');
                      } else {
                          toastr.error("Something went wrong. Please try again later.");
                      }
                  }
              });
          });
      </script>
  @endpush
