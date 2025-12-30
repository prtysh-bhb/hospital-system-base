<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorSchedule;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Get doctors using USER MODEL
        $doctors = User::where('role', 'doctor')->get();

        if ($doctors->isEmpty()) {
            $this->command->warn('No doctors found. DoctorScheduleSeeder skipped.');
            return;
        }

        foreach ($doctors as $doctor) {

            // Each doctor works 5 random weekdays
            $workDays = $this->getRandomWorkDays(5);

            foreach ($workDays as $day) {
                $shift = $this->generateShift($day);

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => $shift['start'],
                        'end_time' => $shift['end'],
                        'slot_duration' => 30,
                        'max_patients' => rand(15, 25),
                        'is_available' => true,
                        'notes' => $this->getDayNote($day),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function getRandomWorkDays(int $count): array
    {
        // 1 = Monday, 5 = Friday
        $days = range(1, 5);
        shuffle($days);

        return array_slice($days, 0, $count);
    }

    private function generateShift(int $day): array
    {
        // Alternate shifts for realism
        if ($day % 2 === 0) {
            return [
                'start' => '09:00:00',
                'end' => '17:00:00',
            ];
        }

        return [
            'start' => '10:00:00',
            'end' => '18:00:00',
        ];
    }

    private function getDayNote(int $day): string
    {
        $notes = [
            'Morning consultations only',
            'Afternoon sessions available',
            'Full day availability',
            'Emergency cases prioritized',
        ];

        return $notes[array_rand($notes)];
    }
}
