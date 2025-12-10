<form autocorrect="off" action="{{ route('admin.specialities-store') }}" autocomplete="off" method="post"
    class="specialities-formsubmit space-y-4 sm:space-y-6" enctype="multipart/form-data">

    {{ csrf_field() }}

    @if (!empty($data->id))
        <input type="hidden" name="id" value="{{ $data->id }}">
    @endif

    <!-- Name Field -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Name <span
                class="text-red-600">*</span></label>
        <input type="text" name="name" id="name" value="{{ $data->name ?? '' }}" pattern="^[a-zA-Z\s]+$"
            title="Only letters and spaces are allowed" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            placeholder="Enter specialty name">
        <span class="text-red-600 text-sm error name_error"></span>
        <p class="text-xs text-gray-500 mt-1">Only letters and spaces allowed</p>
    </div>

    <!-- Description Field -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Description <span
                class="text-red-600">*</span></label>
        <input type="text" name="description" id="description" value="{{ $data->description ?? '' }}"
            pattern="^[a-zA-Z\s.,]+$" title="Only letters, spaces, commas and periods are allowed"
            oninput="this.value = this.value.replace(/[^a-zA-Z\s.,]/g, '')"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent"
            placeholder="Enter description">
        <span class="text-red-600 text-sm error description_error"></span>
        <p class="text-xs text-gray-500 mt-1">Only letters, spaces, commas and periods allowed</p>
    </div>

    <!-- Status Field -->
    <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Status <span
                class="text-red-600">*</span></label>
        <select name="status" id="status"
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            <option value="">Select Status</option>
            <option value="active" {{ ($data->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ ($data->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <span class="text-red-600 text-sm error status_error"></span>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <button type="button" onclick="closeSpecialtyModal()"
            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            Cancel
        </button>
        <button type="submit" class="px-6 py-2 text-white bg-sky-600 rounded-lg hover:bg-sky-700">
            Save Changes
        </button>
    </div>
</form>
