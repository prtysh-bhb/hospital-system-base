<div class="setting-card border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <h4 class="text-base font-semibold text-gray-800 mb-1">{{ $label }}</h4>
            <p class="text-sm text-gray-600">{{ $description }}</p>
        </div>
        <div class="flex items-center space-x-4">
            {{-- <span class="text-sm font-medium {{ $checked ? 'text-green-600' : 'text-gray-700' }}">
                {{ $checked ? $enabledText : $disabledText }}
            </span> --}}
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer setting-input" data-setting-key="{{ $settingKey }}"
                    data-setting-type="boolean" data-category-id="{{ $categoryId }}" {{ $checked ? 'checked' : '' }}>
                <div
                    class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-sky-600">
                </div>
            </label>
        </div>
    </div>
</div>
