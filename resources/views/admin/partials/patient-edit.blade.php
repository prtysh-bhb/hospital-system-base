<form id="editPatientForm" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- First Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span
                    class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ $patient->user->first_name }}" required minlength="2"
                maxlength="255" pattern="[a-zA-Z\s]+"
                title="Name can only contain letters and spaces (minimum 2 characters)"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Last Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span
                    class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ $patient->user->last_name }}" required minlength="2"
                maxlength="255" pattern="[a-zA-Z\s]+"
                title="Name can only contain letters and spaces (minimum 2 characters)"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                    class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ $patient->user->email }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input type="tel" name="phone" value="{{ $patient->user->phone }}" minlength="10" maxlength="15"
                pattern="[0-9]{10,15}" title="Phone number must be 10-15 digits only"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                onblur="if(this.value && /^0+$/.test(this.value)) { alert('Phone number cannot be all zeros'); this.value=''; }"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Date of Birth -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
            <input type="date" name="date_of_birth"
                value="{{ $patient->user->date_of_birth ? \Carbon\Carbon::parse($patient->user->date_of_birth)->format('Y-m-d') : '' }}"
                max="{{ date('Y-m-d') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Gender -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
            <select name="gender"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                <option value="">Select Gender</option>
                <option value="male" {{ $patient->user->gender === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $patient->user->gender === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $patient->user->gender === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Blood Group -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
            <select name="blood_group"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                <option value="">Select Blood Group</option>
                @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                    <option value="{{ $bg }}" {{ $patient->blood_group === $bg ? 'selected' : '' }}>
                        {{ $bg }}</option>
                @endforeach
            </select>
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span
                    class="text-red-500">*</span></label>
            <select name="status" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                <option value="active" {{ $patient->user->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $patient->user->status === 'inactive' ? 'selected' : '' }}>Inactive
                </option>
            </select>
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>
    </div>

    <!-- Address -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
        <textarea name="address" rows="3" minlength="10" maxlength="500" title="Address must be at least 10 characters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">{{ $patient->user->address }}</textarea>
        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
    </div>

    <!-- Emergency Contact -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" value="{{ $patient->emergency_contact_name }}"
                minlength="2" maxlength="255" pattern="[a-zA-Z\s]+"
                title="Name can only contain letters and spaces (minimum 2 characters)"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
            <input type="tel" name="emergency_contact" value="{{ $patient->emergency_contact_phone }}"
                minlength="10" maxlength="15" pattern="[0-9]{10,15}" title="Phone number must be 10-15 digits only"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                onblur="if(this.value && /^0+$/.test(this.value)) { alert('Phone number cannot be all zeros'); this.value=''; }"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="pt-4 border-t border-gray-200">
        <h5 class="text-base font-semibold text-gray-800 mb-4">Medical Information</h5>
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                <textarea name="medical_history" rows="3" maxlength="2000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="Any past medical conditions, surgeries, allergies, etc.">{{ $patient->medical_history }}</textarea>
                <p class="error-message hidden text-red-600 text-sm mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                <textarea name="current_medications" rows="3" maxlength="2000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="List any medications currently taking">{{ $patient->current_medications }}</textarea>
                <p class="error-message hidden text-red-600 text-sm mt-1"></p>
            </div>
        </div>
    </div>

    <!-- Insurance Information -->
    <div class="pt-4 border-t border-gray-200">
        <h5 class="text-base font-semibold text-gray-800 mb-4">Insurance Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Provider</label>
                <input type="text" name="insurance_provider" value="{{ $patient->insurance_provider }}"
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="Insurance company name">
                <p class="error-message hidden text-red-600 text-sm mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Number</label>
                <input type="text" name="insurance_number" value="{{ $patient->insurance_number }}"
                    maxlength="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="Policy/Member number">
                <p class="error-message hidden text-red-600 text-sm mt-1"></p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <button type="button" onclick="closeModal()"
            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Cancel
        </button>
        <button type="button" onclick="savePatient({{ $patient->id }})"
            class="px-6 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
            Save Changes
        </button>
    </div>
</form>
