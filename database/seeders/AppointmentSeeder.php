<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    private int $appointmentCounter = 1;

    public function run(): void
    {
        $doctors = User::where('role', 'doctor')->get();
        $patients = User::where('role', 'patient')->get();
        $frontdesk = User::where('role', 'frontdesk')->first();

        // Safety checks
        if ($doctors->isEmpty() || $patients->isEmpty() || !$frontdesk) {
            $this->command->warn('Doctors, patients, or frontdesk user missing. Seeder skipped.');
            return;
        }

        foreach ($patients as $patient) {
            $numAppointments = rand(2, 3);

            for ($i = 0; $i < $numAppointments; $i++) {

                $doctor = $doctors->random();
                $appointmentDate = Carbon::now()
                    ->subDays(rand(1, 30))
                    ->setTime(rand(9, 16), 0);

                $appointmentNumber = 'APT-' . date('Y') . '-' . str_pad(
                    $this->appointmentCounter++,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                Appointment::updateOrCreate([
                        'appointment_number' => $appointmentNumber,
                    ],
                    [
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'appointment_date' => $appointmentDate->format('Y-m-d'),
                        'appointment_time' => $appointmentDate->format('H:i:s'),
                        'duration_minutes' => 30,
                        'status' => $this->getRandomStatus(),
                        'appointment_type' => $this->getRandomAppointmentType(),
                        'reason_for_visit' => $this->getRandomReason(),
                        'symptoms' => $this->getRandomSymptoms(),
                        'booked_by' => $frontdesk->id,
                        'booked_via' => $this->getRandomBookingMethod(),
                        'reminder_sent' => rand(0, 1),
                        'created_at' => $appointmentDate->copy()->subDays(rand(1, 7)),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];
        $weights = [10, 30, 15, 40, 5];

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

    private function getRandomAppointmentType(): string
    {
        $types = ['consultation', 'follow_up', 'check_up', 'emergency'];
        return $types[array_rand($types)];
    }

    private function getRandomReason(): string
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

    private function getRandomSymptoms(): string
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

    private function getRandomBookingMethod(): string
    {
        $methods = ['online', 'frontdesk', 'phone'];
        return $methods[array_rand($methods)];
    }
}
