@extends('layouts.admin')

@section('title', 'Specialty Details')
@section('page-title', 'Specialty Details')

@section('header-back-button')
    <a href="{{ route('admin.specialities') }}" class="text-gray-600 hover:text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Specialty Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Specialty Information</h3>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Specialty ID</label>
                    <p class="text-gray-800 font-medium">#{{ $specialty->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Specialty Name</label>
                    <p class="text-gray-800 font-medium">{{ $specialty->name }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                    <p class="text-gray-800">{{ $specialty->description ?? 'No description available' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-gray-800">{{ $specialty->created_at->format('F d, Y h:i A') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-gray-800">{{ $specialty->updated_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Doctors with this Specialty -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Doctors with this Specialty</h3>

            @if ($doctors->count() > 0)
                <div class="space-y-3">
                    @foreach ($doctors as $doctor)
                        <div
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                            <div class="flex items-center gap-4">
                                <img src="{{ $doctor->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($doctor->first_name . ' ' . $doctor->last_name) . '&background=10b981&color=fff' }}"
                                    alt="{{ $doctor->first_name }} {{ $doctor->last_name }}" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-800">Dr. {{ $doctor->first_name }}
                                        {{ $doctor->last_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $doctor->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.doctor-view', $doctor->id) }}"
                                class="text-sky-600 hover:text-sky-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500">No doctors assigned to this specialty yet</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 mt-6">
            <a href="{{ route('admin.specialities') }}"
                class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center">
                Back to List
            </a>
            <button onclick="openEditModal({{ $specialty->id }})"
                class="flex-1 px-6 py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium">
                Edit Specialty
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEditModal(id) {
                window.location.href = '{{ route('admin.specialities') }}?edit=' + id;
            }
        </script>
    @endpush
@endsection
