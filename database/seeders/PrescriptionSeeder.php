<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\Appointment;
use Carbon\Carbon;

class PrescriptionSeeder extends Seeder
{
    private int $prescriptionCounter = 1;

    public function run(): void
    {
        // Get completed appointments using MODEL
        $appointments = Appointment::where('status', 'completed')->get();

        if ($appointments->isEmpty()) {
            $this->command->warn('No completed appointments found. PrescriptionSeeder skipped.');
            return;
        }

        // Check the last generated prescription number in the database
        $lastPrescription = Prescription::orderBy('id', 'desc')->first();
        if ($lastPrescription) {
            // Extract the last prescription number and increment the counter
            $lastPrescribedNumber = $lastPrescription->prescription_number;
            $year = date('Y');
            $counter = (int) substr($lastPrescribedNumber, 8); // Extract the counter part (e.g., 0001 from RX-2025-0001)
            $this->prescriptionCounter = $counter + 1;
        }

        foreach ($appointments as $appointment) {

            // 50% chance to create/update prescription
            if (!rand(0, 1)) {
                continue;
            }

            // Generate a unique prescription number
            do {
                $prescriptionNumber = 'RX-' . date('Y') . '-' . str_pad(
                    $this->prescriptionCounter++,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            } while (Prescription::where('prescription_number', $prescriptionNumber)->exists()); // Ensure it's unique

            // Create or update prescription record
            Prescription::updateOrCreate(
                ['appointment_id' => $appointment->id], // Unique key
                [
                    'prescription_number' => $prescriptionNumber,
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'diagnosis' => $this->getRandomDiagnosis(),
                    'medications' => $this->generateMedications(), // JSON cast recommended
                    'instructions' => $this->getRandomInstructions(),
                    'follow_up_date' => Carbon::now()->addDays(rand(7, 30))->format('Y-m-d'),
                    'notes' => 'Take medications as prescribed and follow up if symptoms persist.',
                    'created_at' => $appointment->created_at,
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function getRandomDiagnosis(): string
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

    private function generateMedications(): array
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
            $med['instructions'] = 'Take with food' . (rand(0, 1) ? ', avoid alcohol' : '');
            $medications[] = $med;
        }

        return $medications;
    }

    private function getRandomInstructions(): string
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
