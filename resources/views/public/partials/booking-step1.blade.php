    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <h1 class="text-xl sm:text-2xl font-bold text-sky-700"><a href="{{ route('home') }}">MediCare Hospital</a>
            </h1>
            <p class="text-xs sm:text-sm text-gray-600">Book Your Appointment</p>
        </div>
    </header>

    <!-- Progress Steps -->
    {{-- <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="1">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        1</div>
                    <span class="text-xs sm:text-xs text-sky-600 font-medium text-center">Doctor</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        2</div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center hidden sm:inline">Date & Time</span>
                    <span class="text-xs text-gray-500 text-center sm:hidden">Time</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        3</div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center">Details</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        4</div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center">Confirm</span>
                </div>
            </div>

            <!-- STEP 1: Doctor Selection -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6">Select a Doctor</h2>

                <!-- Specialty Filter -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-3">Filter by Specialty</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            class="px-3 sm:px-4 py-2 bg-sky-600 text-white rounded-lg text-xs sm:text-sm font-medium">All</button>
                        <button
                            class="px-3 sm:px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs sm:text-sm hover:border-sky-600 hover:text-sky-600">Cardiologist</button>
                        <button
                            class="px-3 sm:px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs sm:text-sm hover:border-sky-600 hover:text-sky-600">Pediatrician</button>
                        <button
                            class="px-3 sm:px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs sm:text-sm hover:border-sky-600 hover:text-sky-600">Orthopedic</button>
                    </div>
                </div>

                <!-- Doctor Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <div
                        class="border border-gray-200 rounded-xl p-3 sm:p-4 hover:border-sky-600 hover:shadow-md transition-all cursor-pointer">
                        <div class="flex items-start space-x-3 sm:space-x-4">
                            <img src="https://ui-avatars.com/api/?name=Dr+Rajesh+Sharma&background=0ea5e9&color=fff&size=80"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg flex-shrink-0" alt="Doctor">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm sm:text-base truncate">Dr. Rajesh Sharma
                                </h3>
                                <p class="text-xs sm:text-sm text-sky-600 mb-1 sm:mb-2">Cardiologist</p>
                                <p class="text-xs text-gray-600 mb-0.5 sm:mb-1">MBBS, MD (Cardiology)</p>
                                <p class="text-xs text-gray-600 mb-1 sm:mb-2">12 years experience</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm font-semibold text-gray-800">₹800</span>
                                    <span class="text-xs text-green-600 font-medium">Available Today</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="border border-gray-200 rounded-xl p-3 sm:p-4 hover:border-sky-600 hover:shadow-md transition-all cursor-pointer">
                        <div class="flex items-start space-x-3 sm:space-x-4">
                            <img src="https://ui-avatars.com/api/?name=Dr+Priya+Mehta&background=8b5cf6&color=fff&size=80"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg flex-shrink-0" alt="Doctor">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm sm:text-base truncate">Dr. Priya Mehta
                                </h3>
                                <p class="text-xs sm:text-sm text-purple-600 mb-1 sm:mb-2">Pediatrician</p>
                                <p class="text-xs text-gray-600 mb-0.5 sm:mb-1">MBBS, MD (Pediatrics)</p>
                                <p class="text-xs text-gray-600 mb-1 sm:mb-2">8 years experience</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm font-semibold text-gray-800">₹600</span>
                                    <span class="text-xs text-green-600 font-medium">Available Today</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-end">
                <a href="{{ route('booking', ['step' => 2]) }}"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">Next
                    Step</a>
            </div>
        </div>
    </form> --}}


    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="1">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">

            {{-- SAME STEP BAR (NO CHANGE) --}}
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        1
                    </div>
                    <span class="text-xs sm:text-xs text-sky-600 font-medium text-center">Doctor</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        2
                    </div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center hidden sm:inline">Date & Time</span>
                    <span class="text-xs text-gray-500 text-center sm:hidden">Time</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        3
                    </div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center">Details</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        4
                    </div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center">Confirm</span>
                </div>
            </div>

            <!-- STEP 1: Doctor Selection -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6">Select a Doctor</h2>

                <!-- Specialty Filter -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-3">Filter by Specialty</label>

                    <div class="flex flex-wrap gap-2">

                        {{-- All Button --}}
                        <a href="{{ route('booking', ['step' => 1]) }}"
                            class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium
                    {{ !$specialty_id ? 'bg-sky-600 text-white' : 'bg-white border border-gray-300 text-gray-700' }}">
                            All
                        </a>

                        {{-- Dynamic Specialties --}}
                        @foreach ($specialties as $sp)
                            <a href="{{ route('booking', ['step' => 1, 'specialty_id' => $sp->id]) }}"
                                class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium
                       {{ $specialty_id == $sp->id ? 'bg-sky-600 text-white' : 'bg-white border border-gray-300 text-gray-700' }}">
                                {{ $sp->name }}
                            </a>
                        @endforeach

                    </div>
                </div>

                <!-- Doctor Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    @php
                        $colors = ['a855f7', '86efac', '38bdf8']; // purple, light green, blue
                    @endphp
                    @forelse ($doctors as $doctor)
                        <label
                            class="doctor-card border border-gray-200 rounded-xl p-3 sm:p-4 hover:border-sky-600 hover:shadow-md transition-all cursor-pointer block">
                            <input type="radio" name="doctor_id" value="{{ $doctor->id }}" class="hidden peer">

                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->first_name . ' ' . $doctor->last_name) }}
                                        &background={{ $colors[$loop->index % count($colors)] }}
                                        &color=fff
                                        &size=80"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg flex-shrink-0">

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base truncate">
                                        {{ $doctor->first_name }} {{ $doctor->last_name }}
                                    </h3>

                                    <p class="text-xs sm:text-sm text-sky-600 mb-1 sm:mb-2">
                                        {{ $doctor->doctorProfile->specialty->name ?? 'Unknown' }}
                                    </p>

                                    <p class="text-xs text-gray-600 mb-1 sm:mb-2">
                                        {{ $doctor->doctorProfile->qualification ?? 'NA' }}
                                    </p>

                                    <p class="text-xs text-gray-600 mb-1 sm:mb-2">
                                        {{ $doctor->doctorProfile->experience_years }} years experience
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs sm:text-sm font-semibold text-gray-800">
                                            ₹{{ $doctor->doctorProfile->consultation_fee }}
                                        </span>
                                        <span class="text-xs text-green-600 font-medium">Available Today</span>
                                    </div>
                                </div>
                            </div>

                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">No doctors available for this specialty.</p>
                    @endforelse

                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-end">
                <button type="submit" id="nextBtn" disabled
                    class="px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Next Step
                </button>
            </div>

        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight selected doctor card
            const selectedDoctorId = {{ session('doctor_id') ?? 'null' }};

            document.querySelectorAll('.doctor-card').forEach(card => {
                const radio = card.querySelector('input[type=radio]');

                // Highlight if this doctor is selected
                if (selectedDoctorId && radio && radio.value == selectedDoctorId) {
                    card.classList.add('border-sky-600', 'bg-sky-50', 'shadow-md');
                    card.classList.remove('border-gray-200');
                    radio.checked = true;
                }

                // Click handler
                card.addEventListener('click', function() {
                    // Remove highlight from all cards
                    document.querySelectorAll('.doctor-card').forEach(c => {
                        c.classList.remove('border-sky-600', 'bg-sky-50', 'shadow-md');
                        c.classList.add('border-gray-200');
                    });

                    // Highlight clicked card
                    this.classList.add('border-sky-600', 'bg-sky-50', 'shadow-md');
                    this.classList.remove('border-gray-200');

                    // Select radio
                    radio.checked = true;

                    // Enable Next button
                    const nextBtn = document.getElementById('nextBtn');
                    if (nextBtn) {
                        nextBtn.disabled = false;
                    }
                });
            });
        });
    </script>
