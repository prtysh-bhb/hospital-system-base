<?php

namespace Database\Seeders;

use App\Models\SettingCategory;
use Illuminate\Database\Seeder;

class SettingCategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settingCategories = [
            [
                'name' => 'general',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'public_booking',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'notifications',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'booking_form',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settingCategories as $category) {
            SettingCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
