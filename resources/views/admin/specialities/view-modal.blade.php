<!-- Specialty Information Card -->
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Specialty Information</h3>
        @if ($specialty->status === 'active')
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                Active
            </span>
        @else
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                Inactive
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1">Specialty ID</label>
            <p class="text-sm sm:text-base text-gray-800 font-medium">#{{ $specialty->id }}</p>
        </div>

        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1">Specialty Name</label>
            <p class="text-sm sm:text-base text-gray-800 font-medium">{{ $specialty->name }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1">Description</label>
            <p class="text-sm sm:text-base text-gray-800">{{ $specialty->description ?? 'No description available' }}
            </p>
        </div>

        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1">Created At</label>
            <p class="text-sm sm:text-base text-gray-800">{{ $specialty->created_at->format('F d, Y h:i A') }}</p>
        </div>

        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1">Last Updated</label>
            <p class="text-sm sm:text-base text-gray-800">{{ $specialty->updated_at->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Doctors with this Specialty -->
<div class="bg-gray-50 rounded-lg p-4 sm:p-6">
    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Doctors with this Specialty
        ({{ $doctors->count() }})</h3>

    @if ($doctors->count() > 0)
        <div class="space-y-3 max-h-64 overflow-y-auto">
            @foreach ($doctors as $doctor)
                <div
                    class="flex items-center justify-between p-3 sm:p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <img src="{{ $doctor->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($doctor->first_name . ' ' . $doctor->last_name) . '&background=10b981&color=fff' }}"
                            alt="{{ $doctor->first_name }} {{ $doctor->last_name }}"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full">
                        <div>
                            <p class="text-sm sm:text-base font-medium text-gray-800">Dr. {{ $doctor->first_name }}
                                {{ $doctor->last_name }}</p>
                            <p class="text-xs sm:text-sm text-gray-500">{{ $doctor->email }}</p>
                        </div>
                    </div>
                    <span class="text-xs sm:text-sm text-gray-500">ID: {{ $doctor->id }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-sm sm:text-base text-gray-500">No doctors assigned to this specialty yet</p>
        </div>
    @endif
</div>

<!-- Action Buttons -->
<div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-200">
    <button onclick="closeViewModal()"
        class="flex-1 px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm sm:text-base">
        Close
    </button>
    <button onclick="openEditFromView({{ $specialty->id }})"
        class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium text-sm sm:text-base">
        Edit Specialty
    </button>
</div>

<script>
    function openEditFromView(id) {
        // Close view modal
        closeViewModal();

        // Open edit modal with specialty ID
        setTimeout(function() {
            $('.openaddmodal[data-id="' + id + '"]').trigger('click');
            // Or directly trigger the edit modal
            $.ajax({
                url: "{{ route('admin.specialities-getmodel') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id
                },
                success: function(data) {
                    $('.modal-title').text("Edit Specialty");
                    $('.addmodalbody').html(data);
                    $('.add_modal').removeClass('hidden');
                },
            });
        }, 300);
    }
</script>
