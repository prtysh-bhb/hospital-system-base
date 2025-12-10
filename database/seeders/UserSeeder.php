<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [];

        // Admin Users
        $users[] = [
            'role' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@123'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'phone' => '+1-555-0100',
            'date_of_birth' => '1980-01-15',
            'gender' => 'male',
            'address' => '123 Hospital Ave, Medical City',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Front Desk Staff
        $users[] = [
            'role' => 'frontdesk',
            'email' => 'frontdesk@medicare.com',
            'password' => Hash::make('admin@123'),
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'phone' => '+1-555-0101',
            'date_of_birth' => '1990-03-20',
            'gender' => 'female',
            'address' => '456 Reception St, Medical City',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $users[] = [
            'role' => 'frontdesk',
            'email' => 'reception@medicare.com',
            'password' => Hash::make('admin@123'),
            'first_name' => 'Michael',
            'last_name' => 'Brown',
            'phone' => '+1-555-0102',
            'date_of_birth' => '1992-07-11',
            'gender' => 'male',
            'address' => '789 Front Desk Rd, Medical City',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Doctors (will be linked to specialties)
        $doctors = [
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Sharma',
                'email' => 'dr.sharma@medicare.com',
                'specialty' => 'Cardiology',
                'qualification' => 'MD, DM Cardiology',
                'experience' => 15,
                'fee' => 1500.00,
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Mehta',
                'email' => 'dr.mehta@medicare.com',
                'specialty' => 'Pediatrics',
                'qualification' => 'MD Pediatrics, DCH',
                'experience' => 12,
                'fee' => 1200.00,
            ],
            [
                'first_name' => 'Amit',
                'last_name' => 'Verma',
                'email' => 'dr.verma@medicare.com',
                'specialty' => 'Orthopedics',
                'qualification' => 'MS Orthopedics',
                'experience' => 10,
                'fee' => 1300.00,
            ],
            [
                'first_name' => 'Anita',
                'last_name' => 'Desai',
                'email' => 'dr.desai@medicare.com',
                'specialty' => 'Dermatology',
                'qualification' => 'MD Dermatology',
                'experience' => 8,
                'fee' => 1100.00,
            ],
            [
                'first_name' => 'Sanjay',
                'last_name' => 'Patel',
                'email' => 'dr.patel@medicare.com',
                'specialty' => 'Neurology',
                'qualification' => 'DM Neurology',
                'experience' => 14,
                'fee' => 1600.00,
            ],
        ];

        foreach ($doctors as $doctor) {
            $users[] = [
                'role' => 'doctor',
                'email' => $doctor['email'],
                'password' => Hash::make('admin@123'),
                'first_name' => $doctor['first_name'],
                'last_name' => $doctor['last_name'],
                'phone' => '+1-555-01'.rand(10, 99),
                'date_of_birth' => '197'.rand(5, 9).'-'.rand(1, 12).'-'.rand(1, 28),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'address' => rand(100, 999).' Doctor Lane, Medical City',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Patients
        $patientNames = [
            ['Rahul', 'Kumar'],
            ['Sneha', 'Singh'],
            ['Vikram', 'Yadav'],
            ['Neha', 'Gupta'],
            ['Arun', 'Joshi'],
            ['Pooja', 'Reddy'],
            ['Kiran', 'Malhotra'],
            ['Deepak', 'Shah'],
            ['Meera', 'Iyer'],
            ['Rohan', 'Chopra'],
            ['Anjali', 'Bose'],
            ['Suresh', 'Nair'],
        ];

        foreach ($patientNames as $index => $name) {
            $users[] = [
                'role' => 'patient',
                'email' => strtolower($name[0]).'.'.strtolower($name[1]).'@patient.com',
                'password' => Hash::make('admin@123'),
                'first_name' => $name[0],
                'last_name' => $name[1],
                'phone' => '+1-555-02'.str_pad($index, 2, '0', STR_PAD_LEFT),
                'date_of_birth' => '19'.rand(80, 99).'-'.rand(1, 12).'-'.rand(1, 28),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'address' => rand(100, 999).' Patient Street, Medical City',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);
    }
}
