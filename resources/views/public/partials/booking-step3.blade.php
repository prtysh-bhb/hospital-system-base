    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <h1 class="text-xl sm:text-2xl font-bold text-sky-700"><a href="{{ route('home') }}">MediCare Hospital</a>
            </h1>
            <p class="text-xs sm:text-sm text-gray-600">Book Your Appointment</p>
        </div>
    </header>

    <!-- Progress Steps -->
    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="3">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        ✓</div>
                    <span class="text-xs sm:text-xs text-sky-600 font-medium">Doctor</span>
                </div>
                <div class="flex-1 h-1 bg-sky-600 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        ✓</div>
                    <span class="text-xs sm:text-xs text-sky-600 font-medium text-center">Date & Time</span>
                </div>
                <div class="flex-1 h-1 bg-sky-600 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-600 text-white rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        3</div>
                    <span class="text-xs sm:text-xs text-sky-600 font-medium">Details</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center flex-1">
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold mb-1 sm:mb-2 text-sm sm:text-base">
                        4</div>
                    <span class="text-xs sm:text-xs text-gray-500 text-center">Confirm</span>
                </div>
            </div>

            <!-- Appointment Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-3 sm:mb-4">Appointment Summary</h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->first_name . ' ' . $doctor->last_name) }}&background=0ea5e9&color=fff&size=80"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg" alt="Doctor">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm sm:text-base">{{ $doctor->first_name }}</h3>
                            <p class="text-xs sm:text-sm text-sky-600">
                                {{ $doctor->doctorProfile->specialty->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs sm:text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-800">{{ $selectedSlot }}</p>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Patient Details Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6">Patient Details</h2>

                <div class="space-y-4">

                    <!-- First & Last Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('first_name') border-red-500 @else border-gray-300 @enderror"
                                placeholder="Enter last name">
                            @error('first_name')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('last_name') border-red-500 @else border-gray-300 @enderror"
                                placeholder="Enter last name">
                            @error('last_name')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                    class="text-red-600">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('email') border-red-500 @else border-gray-300 @enderror"
                                placeholder="Enter email">
                            @error('email')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span
                                    class="text-red-600">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15); removeError(this);"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('phone') border-red-500 @else border-gray-300 @enderror"
                                placeholder="Enter phone number">
                            @error('phone')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- DOB & Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span
                                    class="text-red-600">*</span></label>
                            <input type="date" name="date_of_birth" max="{{ date('Y-m-d') }}"
                                value="{{ old('date_of_birth') }}" oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('date_of_birth') border-red-500 @else border-gray-300 @enderror">
                            @error('date_of_birth')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span
                                    class="text-red-600">*</span></label>
                            <select name="gender" oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('gender') border-red-500 @else border-gray-300 @enderror">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="3" oninput="removeError(this)"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('address') border-red-500 @else border-gray-300 @enderror"
                            placeholder="Enter address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason for Visit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Visit <span
                                class="text-red-600">*</span></label>
                        <textarea name="reason_for_visit" rows="3" oninput="removeError(this)"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('reason_for_visit') border-red-500 @else border-gray-300 @enderror"
                            placeholder="Describe your symptoms">{{ old('reason_for_visit') }}</textarea>
                        @error('reason_for_visit')
                            <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                        <input type="text" name="allergies" value="{{ old('allergies') }}"
                            oninput="removeError(this)"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('allergies') border-red-500 @else border-gray-300 @enderror"
                            placeholder="List allergies (optional)">
                        @error('allergies')
                            <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Contact (Conditional) -->
                    @hasAccess('show_emergency_contact')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact
                                    Name</label>
                                <input type="text" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name') }}" oninput="removeError(this)"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('emergency_contact_name') border-red-500 @else border-gray-300 @enderror"
                                    placeholder="Contact person name">
                                @error('emergency_contact_name')
                                    <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact
                                    Phone</label>
                                <input type="text" name="emergency_contact_phone"
                                    value="{{ old('emergency_contact_phone') }}" oninput="removeError(this)"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('emergency_contact_phone') border-red-500 @else border-gray-300 @enderror"
                                    placeholder="Contact phone number">
                                @error('emergency_contact_phone')
                                    <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endhasAccess

                    <!-- Blood Group (Conditional) -->
                    @hasAccess('show_blood_group')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                            <select name="blood_group" oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('blood_group') border-red-500 @else border-gray-300 @enderror">
                                <option value="">Select blood group</option>
                                <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+
                                </option>
                                <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-
                                </option>
                                <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_group')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    @endhasAccess

                    <!-- Medical History (Conditional) -->
                    @hasAccess('show_medical_history')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medical History</label>
                            <textarea name="medical_history" rows="3" oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('medical_history') border-red-500 @else border-gray-300 @enderror"
                                placeholder="Any past medical conditions, surgeries, etc.">{{ old('medical_history') }}</textarea>
                            @error('medical_history')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    @endhasAccess

                    <!-- Current Medications (Conditional) -->
                    @hasAccess('show_current_medications')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Medications</label>
                            <textarea name="current_medications" rows="3" oninput="removeError(this)"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('current_medications') border-red-500 @else border-gray-300 @enderror"
                                placeholder="List any medications you are currently taking">{{ old('current_medications') }}</textarea>
                            @error('current_medications')
                                <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    @endhasAccess

                    <!-- Insurance Details (Conditional) -->
                    @hasAccess('show_insurance_details')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Provider</label>
                                <input type="text" name="insurance_provider" value="{{ old('insurance_provider') }}"
                                    oninput="removeError(this)"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('insurance_provider') border-red-500 @else border-gray-300 @enderror"
                                    placeholder="Insurance company name">
                                @error('insurance_provider')
                                    <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Number</label>
                                <input type="text" name="insurance_number" value="{{ old('insurance_number') }}"
                                    oninput="removeError(this)"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-sky-600 focus:border-transparent @error('insurance_number') border-red-500 @else border-gray-300 @enderror"
                                    placeholder="Policy/Member number">
                                @error('insurance_number')
                                    <p class="text-xs text-red-500 mt-1 error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endhasAccess

                </div>
            </div>

            <!-- Navigation -->
            <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 sm:gap-0 mb-6">
                <a href="{{ route('booking', ['step' => 2]) }}"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-lg text-sm sm:text-base font-medium hover:bg-gray-300 text-center">Back</a>
                <button type="submit"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 bg-sky-600 text-white rounded-lg text-sm sm:text-base font-medium hover:bg-sky-700 text-center">Confirm
                    Appointment</button>
            </div>
        </div>
    </form>
    <script>
        function removeError(input) {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
            const error = input.parentNode.querySelector('.error-msg');
            if (error) error.remove();
        }
    </script>
