@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
    <div class="mx-auto">
        @php
            $generalCat = $categories->where('name', 'general')->first();
            $bookingCat = $categories->where('name', 'public_booking')->first();
            $notifCat = $categories->where('name', 'notifications')->first();
            $formCat = $categories->where('name', 'booking_form')->first();
        @endphp
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar -->
            <div class="lg:w-100 xl:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Sidebar Header -->
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Settings Categories</h3>
                        <p class="text-sm text-gray-500">Manage your system configuration</p>
                    </div>
                    <div class="p-2">
                        <!-- Static categories -->
                        <nav class="space-y-1">
                            <!-- General Settings -->
                            <button type="button" onclick="switchCategory('general')" id="category_general"
                                class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 active-category bg-sky-50 text-sky-700 border border-sky-100">
                                <span
                                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 bg-sky-500 text-white shadow-sm">
                                    <i class="fas fa-cog text-xs"></i>
                                </span>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ $generalCat['display_name'] ?? 'General' }}</div>
                                    <div class="text-xs mt-0.5 text-sky-600">{{ $generalCat['settings_count'] ?? 0 }}
                                        {{ Str::plural('setting', $generalCat['settings_count'] ?? 0) }}</div>
                                </div>
                            </button>

                            <!-- Public Booking Settings -->
                            <button type="button" onclick="switchCategory('public_booking')" id="category_public_booking"
                                class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                <span
                                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 bg-gray-100 text-gray-500">
                                    <i class="fas fa-calendar-check text-xs"></i>
                                </span>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ $bookingCat['display_name'] ?? 'Public Booking' }}</div>
                                    <div class="text-xs mt-0.5 text-gray-400">{{ $bookingCat['settings_count'] ?? 0 }}
                                        {{ Str::plural('setting', $bookingCat['settings_count'] ?? 0) }}</div>
                                </div>
                            </button>

                            <!-- Notifications Settings -->
                            <button type="button" onclick="switchCategory('notifications')" id="category_notifications"
                                class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                <span
                                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 bg-gray-100 text-gray-500">
                                    <i class="fas fa-bell text-xs"></i>
                                </span>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ $notifCat['display_name'] ?? 'Notifications' }}</div>
                                    <div class="text-xs mt-0.5 text-gray-400">{{ $notifCat['settings_count'] ?? 0 }}
                                        {{ Str::plural('setting', $notifCat['settings_count'] ?? 0) }}</div>
                                </div>
                            </button>

                            <!-- Booking Form Settings -->
                            <button type="button" onclick="switchCategory('booking_form')" id="category_booking_form"
                                class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                <span
                                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 bg-gray-100 text-gray-500">
                                    <i class="fas fa-file-medical text-xs"></i>
                                </span>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ $formCat['display_name'] ?? 'Booking Form' }}</div>
                                    <div class="text-xs mt-0.5 text-gray-400">{{ $formCat['settings_count'] ?? 0 }}
                                        {{ Str::plural('setting', $formCat['settings_count'] ?? 0) }}</div>
                                </div>
                            </button>
                        </nav>

                        <!-- Dynamically generate categories (commented out) -->
                        {{-- <nav class="space-y-1">
                            <!-- Loop through all active categories -->
                            @foreach ($categories as $category)
                                <button type="button" onclick="switchCategory('{{ $category['name'] }}')"
                                    id="category_{{ $category['name'] }}"
                                    class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ $loop->first ? 'active-category bg-sky-50 text-sky-700 border border-sky-100' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <span
                                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 {{ $loop->first ? 'bg-sky-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                        <i class="fas fa-cog text-xs"></i>
                                        <!-- You can change this icon per category if you want -->
                                    </span>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">{{ $category['display_name'] }}
                                        </div>
                                        <div class="text-xs mt-0.5 text-sky-600">
                                            {{ $category['settings_count'] ?? 0 }}
                                            {{ Str::plural('setting', $category['settings_count'] ?? 0) }}
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </nav> --}}
                    </div>

                    <!-- Sidebar Footer -->
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        <div class="text-xs text-gray-500">
                            Changes are saved per category
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1">

                <!-- GENERAL SETTINGS -->
                <div id="content_general"
                    class="category-content bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-50 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-cog text-sky-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $generalCat['display_name'] ?? 'General' }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure your
                                        {{ strtolower($generalCat['display_name'] ?? 'general') }} preferences</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full font-medium">
                                    {{ $generalCat['settings_count'] ?? 0 }}
                                    {{ Str::plural('setting', $generalCat['settings_count'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 lg:p-8">
                        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                            <!-- Site Name -->
                            @if (isset($settings['site_name']))
                                <div class="setting-card bg-white border-2 border-gray-100 rounded-xl p-5 hover:border-sky-200 hover:shadow-md transition-all duration-200"
                                    data-category-id="{{ $settings['site_name']['category_id'] }}">
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1 truncate">
                                                {{ ucwords(str_replace('_', ' ', 'site_name')) }}</h4>
                                            <p class="text-xs text-gray-500">{{ $settings['site_name']['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-6 space-y-2">
                                        <label class="text-sm font-medium text-gray-700">Value</label>
                                        <input type="text"
                                            class="setting-input w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all duration-200 hover:border-gray-200"
                                            data-setting-key="site_name"
                                            data-category-id="{{ $settings['site_name']['category_id'] }}"
                                            data-setting-type="{{ $settings['site_name']['type'] }}"
                                            value="{{ $settings['site_name']['value'] }}" placeholder="Enter value">
                                    </div>
                                </div>
                            @endif

                            <!-- Max Appointments Per Day -->
                            @if (isset($settings['max_appointments_per_day']))
                                <div class="setting-card bg-white border-2 border-gray-100 rounded-xl p-5 hover:border-sky-200 hover:shadow-md transition-all duration-200"
                                    data-category-id="{{ $settings['max_appointments_per_day']['category_id'] }}">
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1 truncate">
                                                {{ ucwords(str_replace('_', ' ', 'max_appointments_per_day')) }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ $settings['max_appointments_per_day']['description'] }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 space-y-2">
                                        <label class="text-sm font-medium text-gray-700">Value</label>
                                        <input type="number"
                                            class="setting-input w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all duration-200 hover:border-gray-200"
                                            data-setting-key="max_appointments_per_day"
                                            data-category-id="{{ $settings['max_appointments_per_day']['category_id'] }}"
                                            data-setting-type="{{ $settings['max_appointments_per_day']['type'] }}"
                                            value="{{ $settings['max_appointments_per_day']['value'] }}" min="0"
                                            placeholder="Enter number">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-500">Your changes will be applied immediately after saving
                                </div>
                                <button type="button"
                                    class="save-category-btn inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-blue-700 transition-all duration-200 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none shadow-sm"
                                    data-category-id="{{ $generalCat['id'] ?? 1 }}">
                                    <i class="fas fa-save"></i>
                                    <span>Save {{ $generalCat['display_name'] ?? 'General' }} Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PUBLIC BOOKING SETTINGS -->
                <div id="content_public_booking"
                    class="category-content bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden">
                    <div class="border-b border-gray-100 p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-50 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-sky-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $bookingCat['display_name'] ?? 'Public Booking' }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure your
                                        {{ strtolower($bookingCat['display_name'] ?? 'public booking') }} preferences</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full font-medium">
                                    {{ $bookingCat['settings_count'] ?? 0 }}
                                    {{ Str::plural('setting', $bookingCat['settings_count'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 lg:p-8">
                        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                            <!-- Appointment Booking Days -->
                            @if (isset($settings['appointment_booking_days']))
                                <div class="setting-card bg-white border-2 border-gray-100 rounded-xl p-5 hover:border-sky-200 hover:shadow-md transition-all duration-200"
                                    data-category-id="{{ $settings['appointment_booking_days']['category_id'] }}">
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1 truncate">
                                                {{ ucwords(str_replace('_', ' ', 'appointment_booking_days')) }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ $settings['appointment_booking_days']['description'] }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 space-y-2">
                                        <label class="text-sm font-medium text-gray-700">Value</label>
                                        <input type="number"
                                            class="setting-input w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all duration-200 hover:border-gray-200"
                                            data-setting-key="appointment_booking_days"
                                            data-category-id="{{ $settings['appointment_booking_days']['category_id'] }}"
                                            data-setting-type="{{ $settings['appointment_booking_days']['type'] }}"
                                            value="{{ $settings['appointment_booking_days']['value'] }}" min="0"
                                            placeholder="Enter number">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-500">Your changes will be applied immediately after saving
                                </div>
                                <button type="button"
                                    class="save-category-btn inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-blue-700 transition-all duration-200 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none shadow-sm"
                                    data-category-id="{{ $bookingCat['id'] ?? 2 }}">
                                    <i class="fas fa-save"></i>
                                    <span>Save {{ $bookingCat['display_name'] ?? 'Public Booking' }} Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NOTIFICATIONS SETTINGS -->
                <div id="content_notifications"
                    class="category-content bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden">
                    <div class="border-b border-gray-100 p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-50 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-bell text-sky-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $notifCat['display_name'] ?? 'Notifications' }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure your
                                        {{ strtolower($notifCat['display_name'] ?? 'notifications') }} preferences</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full font-medium">
                                    {{ $notifCat['settings_count'] ?? 0 }}
                                    {{ Str::plural('setting', $notifCat['settings_count'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 lg:p-8">
                        @if (isset($settings['enable_email_notifications']))
                            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                                <!-- Enable Email Notifications -->
                                <div class="setting-card bg-white border-2 border-gray-100 rounded-xl p-5 hover:border-sky-200 hover:shadow-md transition-all duration-200"
                                    data-category-id="{{ $settings['enable_email_notifications']['category_id'] }}">
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1 truncate">
                                                {{ ucwords(str_replace('_', ' ', 'enable_email_notifications')) }}</h4>
                                            <p class="text-xs text-gray-500">
                                                {{ $settings['enable_email_notifications']['description'] }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="text-sm font-medium text-gray-700">{{ $settings['enable_email_notifications']['value'] == '1' ? 'Enabled' : 'Disabled' }}</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer setting-input"
                                                    data-setting-key="enable_email_notifications"
                                                    data-category-id="{{ $settings['enable_email_notifications']['category_id'] }}"
                                                    data-setting-type="{{ $settings['enable_email_notifications']['type'] }}"
                                                    {{ $settings['enable_email_notifications']['value'] == '1' ? 'checked' : '' }}>
                                                <div
                                                    class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-100 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-sky-500 peer-checked:to-blue-500">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-100">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="text-sm text-gray-500">Your changes will be applied immediately after
                                        saving
                                    </div>
                                    <button type="button"
                                        class="save-category-btn inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-blue-700 transition-all duration-200 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none shadow-sm"
                                        data-category-id="{{ $notifCat['id'] ?? 3 }}">
                                        <i class="fas fa-save"></i>
                                        <span>Save {{ $notifCat['display_name'] ?? 'Notifications' }} Settings</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div
                                    class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-bell text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Settings Available</h3>
                                <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                                    There are no configuration options for this category yet. Add new settings as needed.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- BOOKING FORM SETTINGS -->
                <div id="content_booking_form"
                    class="category-content bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden">
                    <div class="border-b border-gray-100 p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-50 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-medical text-sky-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $formCat['display_name'] ?? 'Booking Form' }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure patient form fields visibility</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full font-medium">
                                    {{ $formCat['settings_count'] ?? 0 }}
                                    {{ Str::plural('setting', $formCat['settings_count'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 lg:p-8">
                        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                            <!-- Show Emergency Contact -->
                            @if (isset($settings['show_emergency_contact']))
                                <x-setting-switch-button setting-key="show_emergency_contact"
                                    label="{{ ucwords(str_replace('_', ' ', 'show_emergency_contact')) }}"
                                    description="{{ $settings['show_emergency_contact']['description'] }}"
                                    category-id="{{ $settings['show_emergency_contact']['category_id'] }}"
                                    :checked="$settings['show_emergency_contact']['value'] == '1'" enabled-text="Visible" disabled-text="Hidden" />
                            @endif

                            <!-- Show Blood Group -->
                            @if (isset($settings['show_blood_group']))
                                <x-setting-switch-button setting-key="show_blood_group"
                                    label="{{ ucwords(str_replace('_', ' ', 'show_blood_group')) }}"
                                    description="{{ $settings['show_blood_group']['description'] }}"
                                    category-id="{{ $settings['show_blood_group']['category_id'] }}" :checked="$settings['show_blood_group']['value'] == '1'"
                                    enabled-text="Visible" disabled-text="Hidden" />
                            @endif

                            <!-- Show Medical History -->
                            @if (isset($settings['show_medical_history']))
                                <x-setting-switch-button setting-key="show_medical_history"
                                    label="{{ ucwords(str_replace('_', ' ', 'show_medical_history')) }}"
                                    description="{{ $settings['show_medical_history']['description'] }}"
                                    category-id="{{ $settings['show_medical_history']['category_id'] }}" :checked="$settings['show_medical_history']['value'] == '1'"
                                    enabled-text="Visible" disabled-text="Hidden" />
                            @endif

                            <!-- Show Current Medications -->
                            @if (isset($settings['show_current_medications']))
                                <x-setting-switch-button setting-key="show_current_medications"
                                    label="{{ ucwords(str_replace('_', ' ', 'show_current_medications')) }}"
                                    description="{{ $settings['show_current_medications']['description'] }}"
                                    category-id="{{ $settings['show_current_medications']['category_id'] }}"
                                    :checked="$settings['show_current_medications']['value'] == '1'" enabled-text="Visible" disabled-text="Hidden" />
                            @endif

                            <!-- Show Insurance Details -->
                            @if (isset($settings['show_insurance_details']))
                                <x-setting-switch-button setting-key="show_insurance_details"
                                    label="{{ ucwords(str_replace('_', ' ', 'show_insurance_details')) }}"
                                    description="{{ $settings['show_insurance_details']['description'] }}"
                                    category-id="{{ $settings['show_insurance_details']['category_id'] }}"
                                    :checked="$settings['show_insurance_details']['value'] == '1'" enabled-text="Visible" disabled-text="Hidden" />
                            @endif
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-500">Your changes will be applied immediately after saving
                                </div>
                                <button type="button"
                                    class="save-category-btn inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-blue-700 transition-all duration-200 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none shadow-sm"
                                    data-category-id="{{ $formCat['id'] ?? 5 }}">
                                    <i class="fas fa-save"></i>
                                    <span>Save {{ $formCat['display_name'] ?? 'Booking Form' }} Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Category button hover effects */
        .category-btn:hover .w-9.h-9 {
            background-color: #e0f2fe !important;
            color: #0284c7 !important;
            transform: scale(1.05);
        }

        .active-category {
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        }

        /* Setting card animations */
        .setting-card {
            transition: all 0.3s ease;
        }

        .setting-card:hover {
            transform: translateY(-2px);
        }

        /* Success animation */
        @keyframes flashSuccess {

            0%,
            100% {
                background-color: transparent;
                border-color: #f1f5f9;
            }

            50% {
                background-color: #f0f9ff;
                border-color: #38bdf8;
                box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
            }
        }

        .flash-success {
            animation: flashSuccess 1s ease-in-out;
        }

        /* JSON validation styling */
        .json-valid {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }

        .json-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function switchCategory(categoryId) {
            // Hide all category contents
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all category buttons
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active-category', 'bg-sky-50', 'text-sky-700', 'border', 'border-sky-100');
                btn.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');

                // Reset icon colors
                const iconSpan = btn.querySelector('.w-9.h-9');
                if (iconSpan) {
                    iconSpan.classList.remove('bg-sky-500', 'text-white', 'shadow-sm');
                    iconSpan.classList.add('bg-gray-100', 'text-gray-500');
                }

                // Remove chevron
                const chevron = btn.querySelector('.fa-chevron-right');
                if (chevron) chevron.remove();
            });

            // Show selected category content
            const selectedContent = document.getElementById(`content_${categoryId}`);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');

                // Smooth scroll to top of content
                setTimeout(() => {
                    selectedContent.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }

            // Add active state to selected category button
            const selectedBtn = document.getElementById(`category_${categoryId}`);
            if (selectedBtn) {
                selectedBtn.classList.add('active-category', 'bg-sky-50', 'text-sky-700', 'border', 'border-sky-100');
                selectedBtn.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');

                // Update icon
                const iconSpan = selectedBtn.querySelector('.w-9.h-9');
                if (iconSpan) {
                    iconSpan.classList.add('bg-sky-500', 'text-white', 'shadow-sm');
                    iconSpan.classList.remove('bg-gray-100', 'text-gray-500');
                }

                // Add chevron
                const chevron = document.createElement('i');
                chevron.className = 'fas fa-chevron-right text-sky-500 text-xs ml-2';
                selectedBtn.appendChild(chevron);
            }
        }
        // Initialize - show first category by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstCategoryBtn = document.querySelector('.category-btn');

            if (firstCategoryBtn) {
                const categoryId = firstCategoryBtn.id.replace('category_', '');
                switchCategory(categoryId);

                // optional: agar future use ke liye save rakhna ho
                localStorage.setItem('activeCategory', categoryId);
            }
        });

        // Handle boolean toggle text update
        $(document).on('change', '.setting-input[data-setting-type="boolean"]', function() {
            const $card = $(this).closest('.setting-card');
            const $statusText = $card.find(
                '.text-sm.font-medium.text-gray-700, .text-sm.font-medium.text-green-600');
            const settingKey = $(this).data('setting-key');

            if ($(this).is(':checked')) {
                // Check if it's a visibility setting (show_*)
                if (settingKey && settingKey.startsWith('show_')) {
                    $statusText.text('Visible').removeClass('text-gray-700').addClass('text-green-600');
                } else {
                    $statusText.text('Enabled').removeClass('text-gray-700').addClass('text-green-600');
                }
            } else {
                // Check if it's a visibility setting (show_*)
                if (settingKey && settingKey.startsWith('show_')) {
                    $statusText.text('Hidden').removeClass('text-green-600').addClass('text-gray-700');
                } else {
                    $statusText.text('Disabled').removeClass('text-green-600').addClass('text-gray-700');
                }
            }

            // Visual feedback
            $card.addClass('flash-success');
            setTimeout(() => {
                $card.removeClass('flash-success');
            }, 1000);
        });

        // Real-time JSON validation
        $(document).on('input', '.setting-input[data-setting-type="json"]', function() {
            const $textarea = $(this);
            const $icon = $textarea.siblings('.absolute').find('i');
            const $card = $textarea.closest('.setting-card');

            try {
                if ($textarea.val().trim()) {
                    JSON.parse($textarea.val());
                    $icon.removeClass('text-gray-400 text-red-400').addClass('text-green-500');
                    $textarea.removeClass('json-invalid').addClass('json-valid');
                    $card.find('.bg-amber-100').addClass('bg-green-100 text-green-800');
                } else {
                    $icon.removeClass('text-red-400 text-green-500').addClass('text-gray-400');
                    $textarea.removeClass('json-invalid json-valid');
                    $card.find('.bg-amber-100, .bg-green-100').removeClass('bg-green-100 text-green-800').addClass(
                        'bg-amber-100 text-amber-800');
                }
            } catch (e) {
                $icon.removeClass('text-gray-400 text-green-500').addClass('text-red-400');
                $textarea.addClass('json-invalid').removeClass('json-valid');
                $card.find('.bg-amber-100, .bg-green-100').removeClass('bg-green-100 text-green-800').addClass(
                    'bg-red-100 text-red-800');
            }
        });

        // Save all settings in a category
        $(document).on('click', '.save-category-btn', function() {
            const $btn = $(this);
            const categoryId = $btn.data('category-id');
            const $inputs = $(`.setting-input[data-category-id="${categoryId}"]`);
            const categoryName = $btn.find('span').text().replace('Save ', '').replace(' Settings', '');

            // Collect all settings data for this category
            const settings = [];
            let hasError = false;

            $inputs.each(function() {
                const $input = $(this);
                const settingKey = $input.data('setting-key');
                const settingType = $input.data('setting-type');

                let value;
                if (settingType === 'boolean') {
                    value = $input.is(':checked') ? '1' : '0';
                } else {
                    value = $input.val();
                }

                // Validate JSON if type is json
                if (settingType === 'json' && value) {
                    try {
                        JSON.parse(value);
                    } catch (e) {
                        toastr.error('Invalid JSON format in one of the fields');
                        $input.addClass('json-invalid');
                        hasError = true;
                        return false;
                    }
                }

                settings.push({
                    key: settingKey,
                    value: value,
                    type: settingType,
                    category_id: categoryId
                });
            });

            if (hasError) return;

            const originalText = $btn.html();
            $btn.prop('disabled', true).html(`
                <i class="fas fa-spinner fa-spin"></i>
                <span>Saving...</span>
            `);

            $.ajax({
                url: '{{ route('admin.settings.update') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    settings: settings
                },
                success: function(response) {
                    $btn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        toastr.success(response.message ||
                            `${categoryName} settings updated successfully`);

                        // Visual feedback for all updated cards
                        $inputs.each(function() {
                            const $card = $(this).closest('.setting-card');
                            $card.addClass('flash-success');

                            // Special effect for toggle switches
                            if ($(this).data('setting-type') === 'boolean') {
                                const $toggle = $card.find('.peer-checked\\:from-sky-500');
                                $toggle.addClass('animate-pulse');
                                setTimeout(() => {
                                    $toggle.removeClass('animate-pulse');
                                }, 1000);
                            }

                            setTimeout(() => {
                                $card.removeClass('flash-success');
                            }, 1000);
                        });
                    } else {
                        toastr.error(response.message || 'Failed to update settings');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(originalText);
                    const errorMsg = xhr.responseJSON?.message || 'Server Error. Please try again.';
                    toastr.error(errorMsg);

                    // Show error state
                    $inputs.each(function() {
                        $(this).addClass('border-red-300');
                        setTimeout(() => {
                            $(this).removeClass('border-red-300');
                        }, 3000);
                    });
                }
            });
        });

        // Initialize JSON validation on load
        $(document).ready(function() {
            $('.setting-input[data-setting-type="json"]').trigger('input');

            // Make sidebar sticky on larger screens
            if (window.innerWidth >= 1024) {
                $('.lg\\:w-64').css('position', 'sticky').css('top', '6rem');
            }
        });
    </script>
@endpush
