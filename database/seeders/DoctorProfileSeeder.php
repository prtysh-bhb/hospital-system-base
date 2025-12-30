<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Specialty;

class DoctorProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Get specialties using MODEL (name => id)
        $specialties = Specialty::pluck('id', 'name');

        if ($specialties->isEmpty()) {
            $this->command->warn('No specialties found. DoctorProfileSeeder skipped.');
            return;
        }

        // Get doctors using USER MODEL
        $doctors = User::where('role', 'doctor')->get();

        if ($doctors->isEmpty()) {
            $this->command->warn('No doctors found. DoctorProfileSeeder skipped.');
            return;
        }

        $doctorData = [
            'dr.sharma@medicare.com' => [
                'specialty' => 'Cardiology',
                'qualification' => 'MD, DM Cardiology',
                'experience_years' => 15,
                'consultation_fee' => 1500.00,
                'bio' => 'Senior Cardiologist with 15 years of experience in interventional cardiology.',
                'license_number' => 'MED-CARD-' . rand(10000, 99999),
            ],
            'mehta@medicare.com' => [
                'specialty' => 'Pediatrics',
                'qualification' => 'MD Pediatrics, DCH',
                'experience_years' => 12,
                'consultation_fee' => 1200.00,
                'bio' => 'Pediatric specialist focused on child healthcare and development.',
                'license_number' => 'MED-PED-' . rand(10000, 99999),
            ],
            'verma@medicare.com' => [
                'specialty' => 'Orthopedics',
                'qualification' => 'MS Orthopedics',
                'experience_years' => 10,
                'consultation_fee' => 1300.00,
                'bio' => 'Orthopedic surgeon specializing in joint replacement and sports injuries.',
                'license_number' => 'MED-ORT-' . rand(10000, 99999),
            ],
            'desai@medicare.com' => [
                'specialty' => 'Dermatology',
                'qualification' => 'MD Dermatology',
                'experience_years' => 8,
                'consultation_fee' => 1100.00,
                'bio' => 'Dermatologist with expertise in cosmetic and medical dermatology.',
                'license_number' => 'MED-DER-' . rand(10000, 99999),
            ],
            'patel@medicare.com' => [
                'specialty' => 'Neurology',
                'qualification' => 'DM Neurology',
                'experience_years' => 14,
                'consultation_fee' => 1600.00,
                'bio' => 'Neurologist specializing in stroke management and neurological disorders.',
                'license_number' => 'MED-NEU-' . rand(10000, 99999),
            ],
        ];

        foreach ($doctors as $doctor) {

            if (!isset($doctorData[$doctor->email])) {
                continue;
            }

            $data = $doctorData[$doctor->email];

            // Safety check
            if (!isset($specialties[$data['specialty']])) {
                continue;
            }

            DoctorProfile::updateOrCreate(
                [
                    'user_id' => $doctor->id,
                ],
                [
                    'specialty_id' => $specialties[$data['specialty']],
                    'qualification' => $data['qualification'],
                    'experience_years' => $data['experience_years'],
                    'consultation_fee' => $data['consultation_fee'],
                    'bio' => $data['bio'],
                    'license_number' => $data['license_number'],
                    'available_for_booking' => true,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
