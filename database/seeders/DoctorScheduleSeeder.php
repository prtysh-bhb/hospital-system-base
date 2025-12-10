<?php

// database/seeders/DoctorScheduleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorScheduleSeeder extends Seeder
{
    public function run()
    {
        $doctors = DB::table('users')->where('role', 'doctor')->get();

        $schedules = [];

        foreach ($doctors as $doctor) {
            // Each doctor works 5 days a week with different schedules
            $workDays = $this->getRandomWorkDays(5);

            foreach ($workDays as $day) {
                $shift = $this->generateShift($day);

                $schedules[] = [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => $shift['start'],
                    'end_time' => $shift['end'],
                    'slot_duration' => 30,
                    'max_patients' => rand(15, 25),
                    'is_available' => true,
                    'notes' => $this->getDayNote($day),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('doctor_schedules')->insert($schedules);
    }

    private function getRandomWorkDays($count)
    {
        $days = range(1, 5); // Monday to Friday
        shuffle($days);

        return array_slice($days, 0, $count);
    }

    private function generateShift($day)
    {
        // Morning shift for some days, afternoon for others
        if ($day % 2 == 0) {
            return ['start' => '09:00:00', 'end' => '17:00:00'];
        } else {
            return ['start' => '10:00:00', 'end' => '18:00:00'];
        }
    }

    private function getDayNote($day)
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
