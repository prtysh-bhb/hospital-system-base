@php
    $colors = [
        [
            'gradient' => 'from-slate-700 to-slate-800',
            'avatar' => '475569',
            'text' => 'text-slate-700',
            'accent' => 'bg-slate-700',
            'accent-light' => 'bg-slate-50',
            'border' => 'border-slate-200',
            'hover' => 'hover:bg-slate-800',
            'ring' => 'focus:ring-slate-500',
        ],
        [
            'gradient' => 'from-blue-900 to-blue-950',
            'avatar' => '1e3a8a',
            'text' => 'text-blue-900',
            'accent' => 'bg-blue-900',
            'accent-light' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'hover' => 'hover:bg-blue-950',
            'ring' => 'focus:ring-blue-700',
        ],
        [
            'gradient' => 'from-teal-800 to-teal-900',
            'avatar' => '115e59',
            'text' => 'text-teal-800',
            'accent' => 'bg-teal-800',
            'accent-light' => 'bg-teal-50',
            'border' => 'border-teal-200',
            'hover' => 'hover:bg-teal-900',
            'ring' => 'focus:ring-teal-600',
        ],
        [
            'gradient' => 'from-gray-700 to-gray-800',
            'avatar' => '4b5563',
            'text' => 'text-gray-700',
            'accent' => 'bg-gray-700',
            'accent-light' => 'bg-gray-50',
            'border' => 'border-gray-200',
            'hover' => 'hover:bg-gray-800',
            'ring' => 'focus:ring-gray-500',
        ],
        [
            'gradient' => 'from-indigo-900 to-indigo-950',
            'avatar' => '312e81',
            'text' => 'text-indigo-900',
            'accent' => 'bg-indigo-900',
            'accent-light' => 'bg-indigo-50',
            'border' => 'border-indigo-200',
            'hover' => 'hover:bg-indigo-950',
            'ring' => 'focus:ring-indigo-700',
        ],
    ];
@endphp

@foreach ($doctors as $index => $doctor)
    @php
        $color = $colors[$index % count($colors)];
    @endphp
    @if ($doctor->user)
        <div
            class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">

            <!-- Minimal Header Bar -->
            <div class="h-1.5 bg-gradient-to-r {{ $color['gradient'] }}"></div>

            <div class="p-6">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-start space-x-4">
                        <!-- Professional Avatar -->
                        <div class="relative flex-shrink-0">
                            @if ($doctor->user->profile_image)
                                <img src="{{ asset($doctor->user->profile_image) }}"
                                    class="w-16 h-16 rounded-lg object-cover border border-gray-200"
                                    alt="{{ $doctor->user->full_name }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->user->full_name) }}&background={{ $color['avatar'] }}&color=fff&size=128"
                                    class="w-16 h-16 rounded-lg border border-gray-200 hidden"
                                    alt="{{ $doctor->user->full_name }}" style="display:none;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->user->full_name) }}&background={{ $color['avatar'] }}&color=fff&size=128"
                                    class="w-16 h-16 rounded-lg border border-gray-200"
                                    alt="{{ $doctor->user->full_name }}">
                            @endif

                            <!-- Availability Indicator -->
                            @php
                                $statusColor = match ($doctor->user->status) {
                                    'active' => 'bg-emerald-500', // green
                                    'inactive' => 'bg-gray-300', // gray
                                    'suspended' => 'bg-amber-500', // amber/yellow
                                    default => 'bg-gray-300',
                                };
                            @endphp

                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $statusColor }}">
                            </div>
                        </div>

                        <!-- Doctor Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-0.5">{{ $doctor->user->full_name }}</h3>
                            <p class="text-sm font-medium {{ $color['text'] }} mb-1">
                                {{ $doctor->specialty->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $doctor->qualification ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex-shrink-0 ml-2">
                        @if ($doctor->user->status === 'active')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Active
                            </span>
                        @elseif($doctor->user->status === 'inactive')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                Inactive
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                {{ ucfirst($doctor->user->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Experience Badge -->
                <div class="mb-4 pb-4 border-b border-gray-100">
                    <div class="inline-flex items-center text-xs text-gray-600">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">{{ $doctor->experience_years }} years of experience</span>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-2.5 mb-5">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ $doctor->user->phone ?? 'N/A' }}</span>
                    </div>

                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate">{{ $doctor->user->email ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Consultation Fee -->
                <div class="bg-gray-50 rounded-lg px-4 py-3 mb-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Consultation Fee</span>
                        <span
                            class="text-lg font-semibold text-gray-900">₹{{ number_format($doctor->consultation_fee, 0) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button data-doctor-id="{{ $doctor->user->id }}"
                        class="view-doctor-btn flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $color['ring'] }}">
                        View Details
                    </button>
                    <a href="{{ route('admin.doctors.edit', $doctor->user->id) }}"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white {{ $color['accent'] }} rounded-lg {{ $color['hover'] }} transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $color['ring'] }} text-center">
                        Edit Profile
                    </a>
                </div>

                <button data-doctor-id="{{ $doctor->user->id }}"
                    data-doctor-name="{{ $doctor->user->first_name }} {{ $doctor->user->last_name }}"
                    class="delete-doctor-btn w-full mt-2 px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Remove Doctor
                </button>
            </div>
        </div>
    @endif
@endforeach

@if ($doctors->isEmpty())
    <div class="col-span-full">
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg mb-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No doctors found</h3>
            <p class="text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
        </div>
    </div>
@endif
