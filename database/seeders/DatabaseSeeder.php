<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            SpecialtiesSeeder::class,
            UserSeeder::class,
            DoctorProfileSeeder::class,
            PatientProfileSeeder::class,
            DoctorScheduleSeeder::class,
            AppointmentSeeder::class,
            PrescriptionSeeder::class,
            SettingCategories::class,
            SettingsSeeder::class,
            WhatsappTemplateSeeder::class,
        ]);
    }
}
