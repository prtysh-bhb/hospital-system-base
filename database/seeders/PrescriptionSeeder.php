<?php

// database/seeders/PrescriptionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrescriptionSeeder extends Seeder
{
    private $prescriptionCounter = 1;

    public function run()
    {
        // Get completed appointments
        $appointments = DB::table('appointments')
            ->where('status', 'completed')
            ->get();

        $prescriptions = [];

        foreach ($appointments as $appointment) {
            if (rand(0, 1)) { // 50% chance of prescription
                $prescriptions[] = [
                    'prescription_number' => 'RX-'.date('Y').'-'.str_pad($this->prescriptionCounter++, 4, '0', STR_PAD_LEFT),
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'diagnosis' => $this->getRandomDiagnosis(),
                    'medications' => json_encode($this->generateMedications()),
                    'instructions' => $this->getRandomInstructions(),
                    'follow_up_date' => now()->addDays(rand(7, 30))->format('Y-m-d'),
                    'notes' => 'Take medications as prescribed and follow up if symptoms persist.',
                    'created_at' => $appointment->created_at,
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('prescriptions')->insert($prescriptions);
    }

    private function getRandomDiagnosis()
    {
        $diagnoses = [
            'Upper respiratory infection',
            'Hypertension stage 1',
            'Type 2 Diabetes Mellitus',
            'Migraine without aura',
            'Allergic rhinitis',
            'Gastroesophageal reflux disease',
            'Musculoskeletal strain',
        ];

        return $diagnoses[array_rand($diagnoses)];
    }

    private function generateMedications()
    {
        $medications = [];
        $numMeds = rand(1, 3);

        $commonMeds = [
            ['name' => 'Amoxicillin', 'dosage' => '500mg', 'frequency' => '3 times daily', 'duration' => '7 days'],
            ['name' => 'Ibuprofen', 'dosage' => '400mg', 'frequency' => 'As needed for pain', 'duration' => '5 days'],
            ['name' => 'Lisinopril', 'dosage' => '10mg', 'frequency' => 'Once daily', 'duration' => '30 days'],
            ['name' => 'Metformin', 'dosage' => '500mg', 'frequency' => 'Twice daily', 'duration' => '30 days'],
            ['name' => 'Atorvastatin', 'dosage' => '20mg', 'frequency' => 'Once at bedtime', 'duration' => '30 days'],
            ['name' => 'Cetirizine', 'dosage' => '10mg', 'frequency' => 'Once daily', 'duration' => '10 days'],
        ];

        shuffle($commonMeds);

        for ($i = 0; $i < $numMeds; $i++) {
            $med = $commonMeds[$i];
            $med['instructions'] = 'Take with food'.(rand(0, 1) ? ', avoid alcohol' : '');
            $medications[] = $med;
        }

        return $medications;
    }

    private function getRandomInstructions()
    {
        $instructions = [
            'Complete the full course of antibiotics',
            'Monitor blood pressure twice daily',
            'Follow up in 2 weeks',
            'Maintain regular exercise and diet',
            'Avoid strenuous activities',
            'Get adequate rest and hydration',
        ];

        return $instructions[array_rand($instructions)];
    }
}
