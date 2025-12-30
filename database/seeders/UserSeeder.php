<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |------------------------------------------------------------------
        | ADMIN USERS
        |------------------------------------------------------------------
        */
        $admins = [
            [
                'role' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin@123',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => $this->Phonegenerator(),
                'date_of_birth' => '1980-01-15',
                'gender' => 'male',
                'address' => '123 Hospital Ave, Medical City',
                'status' => 'active',
            ],
        ];

        $this->upsertUsers($admins);

        /*
        |------------------------------------------------------------------
        | FRONT DESK USERS
        |------------------------------------------------------------------
        */
        $frontdesks = [
            [
                'role' => 'frontdesk',
                'email' => 'frontdesk@medicare.com',
                'password' => 'admin@123',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'phone' => $this->Phonegenerator(),
                'date_of_birth' => '1990-03-20',
                'gender' => 'female',
                'address' => '456 Reception St, Medical City',
                'status' => 'active',
            ],
            [
                'role' => 'frontdesk',
                'email' => 'reception@medicare.com',
                'password' => 'admin@123',
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'phone' => $this->Phonegenerator(),
                'date_of_birth' => '1992-07-11',
                'gender' => 'male',
                'address' => '789 Front Desk Rd, Medical City',
                'status' => 'active',
            ],
        ];

        $this->upsertUsers($frontdesks);

        /*
        |------------------------------------------------------------------
        | DOCTORS
        |------------------------------------------------------------------
        */
        $doctors = [
            ['Rajesh', 'Sharma', 'dr.sharma@medicare.com'],
            ['Priya', 'Mehta', 'mehta@medicare.com'],
            ['Amit', 'Verma', 'verma@medicare.com'],
            ['Anita', 'Desai', 'desai@medicare.com'],
            ['Sanjay', 'Patel', 'patel@medicare.com'],
        ];

        foreach ($doctors as $doctor) {
            User::updateOrCreate(
                ['email' => $doctor[2]],
                [
                    'role' => 'doctor',
                    'username' => $doctor[2],
                    'password' => Hash::make('admin@123'),
                    'first_name' => $doctor[0],
                    'last_name' => $doctor[1],
                    'phone' => $this->Phonegenerator(),
                    'date_of_birth' => '197' . rand(5, 9) . '-' . rand(1, 12) . '-' . rand(1, 28),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'address' => rand(100, 999) . ' Doctor Lane, Medical City',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |------------------------------------------------------------------
        | PATIENTS
        |------------------------------------------------------------------
        */
        $patients = [
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

        foreach ($patients as $patient) {

            $email = strtolower($patient[0]) . '.' . strtolower($patient[1]) . '@patient.com';
            $phone = $this->Phonegenerator();

            User::updateOrCreate(
                ['email' => $email],
                [
                    'role' => 'patient',
                    'username' => $email,
                    'password' => Hash::make($phone), // password = phone
                    'first_name' => $patient[0],
                    'last_name' => $patient[1],
                    'phone' => $phone,
                    'date_of_birth' => '19' . rand(80, 99) . '-' . rand(1, 12) . '-' . rand(1, 28),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'address' => rand(100, 999) . ' Patient Street, Medical City',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /*
    |------------------------------------------------------------------
    | COMMON UPSERT
    |------------------------------------------------------------------
    */
    private function upsertUsers(array $users): void
    {
        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'role' => $user['role'],
                    'username' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'phone' => $user['phone'],
                    'date_of_birth' => $user['date_of_birth'],
                    'gender' => $user['gender'],
                    'address' => $user['address'],
                    'status' => $user['status'],
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /*
    |------------------------------------------------------------------
    | PHONE GENERATOR
    |------------------------------------------------------------------
    */
    private function Phonegenerator(): string
    {
        return '91' . rand(6000000000, 9999999999);
    }
}
