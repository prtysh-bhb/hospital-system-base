<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SettingCategory;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Get active categories with their settings count
        $categories = SettingCategory::where('status', '1')
            ->orderByRaw('`order` = 0')
            ->orderBy('order', 'asc')
            ->get()->map(function ($category) {
                $settingsCount = Setting::where('setting_category_id', $category->id)
                ->where('status', '1')
                ->count();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'display_name' => ucwords(str_replace('_', ' ', $category->name)),
                    'settings_count' => $settingsCount,
                ];
        });

        // Get all settings grouped by category
        $settingsByCategory = Setting::whereIn('setting_category_id', $categories->pluck('id'))
            ->where('status', '1')
            ->get()
            ->groupBy('setting_category_id');

        // Format settings data for easy access
        $settings = [];
        foreach ($settingsByCategory as $categoryId => $categorySettings) {
            foreach ($categorySettings as $setting) {
                $settings[$setting->key] = [
                    'value' => $setting->getRawOriginal('value'),
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'category_id' => $setting->setting_category_id,
                ];
            }
        }

        // Debug: Uncomment to see data structure
        // dd($categories->toArray(), $settings);

        return view('admin.settings.index', compact('categories', 'settings'));
    }

    /**
     * Update multiple settings (for category-based saving)
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|string',
            'settings.*.category_id' => 'required|integer',
        ]);

        try {
            foreach ($request->settings as $settingData) {
                $value = $settingData['value'];
                $type = $settingData['type'];
                $key = $settingData['key'];
                $categoryId = $settingData['category_id'];

                // Validate and process value based on type
                switch ($type) {
                    case 'integer':
                        if ($value !== null && $value !== '' && ! is_numeric($value)) {
                            return response()->json([
                                'success' => false,
                                'message' => "Value for '{$key}' must be a number",
                            ], 422);
                        }
                        $value = (string) intval($value);
                        break;

                    case 'boolean':
                        $value = ($value === '1' || $value === 'true' || $value === true) ? '1' : '0';
                        break;

                    case 'json':
                        // Validate JSON format
                        if ($value && json_decode($value) === null && json_last_error() !== JSON_ERROR_NONE) {
                            return response()->json([
                                'success' => false,
                                'message' => "Invalid JSON format for '{$key}'",
                            ], 422);
                        }
                        break;

                    default:
                        $value = (string) $value;
                }

                // Update or create the setting
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'type' => $type,
                        'setting_category_id' => $categoryId,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: '.$e->getMessage(),
            ], 500);
        }
    }
}
