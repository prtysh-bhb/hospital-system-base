<?php

// database/seeders/AppointmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    private $appointmentCounter = 1;

    public function run()
    {
        $doctors = DB::table('users')->where('role', 'doctor')->get();
        $patients = DB::table('users')->where('role', 'patient')->get();
        $frontdesk = DB::table('users')->where('role', 'frontdesk')->first();

        $appointments = [];

        // Create 2-3 appointments for each patient
        foreach ($patients as $patient) {
            $numAppointments = rand(2, 3);

            for ($i = 0; $i < $numAppointments; $i++) {
                $doctor = $doctors->random();
                $appointmentDate = now()->subDays(rand(1, 30))->addHours(rand(9, 16));

                $appointments[] = [
                    'appointment_number' => 'APT-'.date('Y').'-'.str_pad($this->appointmentCounter++, 4, '0', STR_PAD_LEFT),
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_date' => $appointmentDate->format('Y-m-d'),
                    'appointment_time' => $appointmentDate->format('H:i:00'),
                    'duration_minutes' => 30,
                    'status' => $this->getRandomStatus(),
                    'appointment_type' => $this->getRandomAppointmentType(),
                    'reason_for_visit' => $this->getRandomReason(),
                    'symptoms' => $this->getRandomSymptoms(),
                    'booked_by' => $frontdesk->id,
                    'booked_via' => $this->getRandomBookingMethod(),
                    'reminder_sent' => rand(0, 1),
                    'created_at' => $appointmentDate->subDays(rand(1, 7)),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('appointments')->insert($appointments);
    }

    private function getRandomStatus()
    {
        $statuses = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];
        $weights = [10, 30, 15, 40, 5]; // Weighted probabilities
        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }

        return 'completed';
    }

    private function getRandomAppointmentType()
    {
        $types = ['consultation', 'follow_up', 'check_up', 'emergency'];

        return $types[array_rand($types)];
    }

    private function getRandomReason()
    {
        $reasons = [
            'Routine checkup',
            'Follow-up consultation',
            'Pain management',
            'Test results discussion',
            'New symptoms evaluation',
            'Prescription renewal',
        ];

        return $reasons[array_rand($reasons)];
    }

    private function getRandomSymptoms()
    {
        $symptoms = [
            'Fever and cough',
            'Headache and dizziness',
            'Joint pain',
            'Chest discomfort',
            'Skin rash',
            'Digestive issues',
            'Fatigue and weakness',
        ];

        return $symptoms[array_rand($symptoms)];
    }

    private function getRandomBookingMethod()
    {
        $methods = ['online', 'frontdesk', 'phone'];

        return $methods[array_rand($methods)];
    }
}
