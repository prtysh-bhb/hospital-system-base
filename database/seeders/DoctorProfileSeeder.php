<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $specialties = DB::table('specialties')->pluck('id', 'name');
        $doctors = DB::table('users')->where('role', 'doctor')->get();

        $doctorProfiles = [];

        $doctorData = [
            'dr.sharma@medicare.com' => [
                'specialty' => 'Cardiology',
                'qualification' => 'MD, DM Cardiology',
                'experience_years' => 15,
                'consultation_fee' => 1500.00,
                'bio' => 'Senior Cardiologist with 15 years of experience in interventional cardiology.',
                'license_number' => 'MED-CARD-'.rand(10000, 99999),
            ],
            'dr.mehta@medicare.com' => [
                'specialty' => 'Pediatrics',
                'qualification' => 'MD Pediatrics, DCH',
                'experience_years' => 12,
                'consultation_fee' => 1200.00,
                'bio' => 'Pediatric specialist focused on child healthcare and development.',
                'license_number' => 'MED-PED-'.rand(10000, 99999),
            ],
            'dr.verma@medicare.com' => [
                'specialty' => 'Orthopedics',
                'qualification' => 'MS Orthopedics',
                'experience_years' => 10,
                'consultation_fee' => 1300.00,
                'bio' => 'Orthopedic surgeon specializing in joint replacement and sports injuries.',
                'license_number' => 'MED-ORT-'.rand(10000, 99999),
            ],
            'dr.desai@medicare.com' => [
                'specialty' => 'Dermatology',
                'qualification' => 'MD Dermatology',
                'experience_years' => 8,
                'consultation_fee' => 1100.00,
                'bio' => 'Dermatologist with expertise in cosmetic and medical dermatology.',
                'license_number' => 'MED-DER-'.rand(10000, 99999),
            ],
            'dr.patel@medicare.com' => [
                'specialty' => 'Neurology',
                'qualification' => 'DM Neurology',
                'experience_years' => 14,
                'consultation_fee' => 1600.00,
                'bio' => 'Neurologist specializing in stroke management and neurological disorders.',
                'license_number' => 'MED-NEU-'.rand(10000, 99999),
            ],
        ];

        foreach ($doctors as $doctor) {
            if (isset($doctorData[$doctor->email])) {
                $data = $doctorData[$doctor->email];
                $doctorProfiles[] = [
                    'user_id' => $doctor->id,
                    'specialty_id' => $specialties[$data['specialty']],
                    'qualification' => $data['qualification'],
                    'experience_years' => $data['experience_years'],
                    'consultation_fee' => $data['consultation_fee'],
                    'bio' => $data['bio'],
                    'license_number' => $data['license_number'],
                    'available_for_booking' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('doctor_profiles')->insert($doctorProfiles);
    }
}
