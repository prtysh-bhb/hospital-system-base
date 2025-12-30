<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PatientProfile;

class PatientProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Get patients using User MODEL
        $patients = User::where('role', 'patient')->get();

        if ($patients->isEmpty()) {
            $this->command->warn('No patients found. PatientProfileSeeder skipped.');
            return;
        }

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $allergies = ['Penicillin', 'Aspirin', 'Dust', 'Pollen', 'Shellfish', 'Latex'];
        $medications = ['Metformin', 'Insulin', 'Lisinopril', 'Atorvastatin', 'Levothyroxine'];
        $insuranceProviders = ['MediHealth', 'CarePlus', 'HealthGuard', 'SecureLife', 'Wellness Inc'];

        foreach ($patients as $patient) {

            $hasAllergies = rand(0, 1);
            $hasMeds = rand(0, 1);

            PatientProfile::updateOrCreate(
                [
                    'user_id' => $patient->id,
                ],
                [
                    'emergency_contact_name' => 'Emergency Contact ' . $patient->last_name,
                    'emergency_contact_phone' => $this->Phonegenerator(),
                    'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                    'allergies' => $hasAllergies
                        ? $allergies[array_rand($allergies)]
                        : 'None',
                    'medical_history' => $this->generateMedicalHistory(),
                    'current_medications' => $hasMeds
                        ? $medications[array_rand($medications)]
                        : 'None',
                    'insurance_provider' => $insuranceProviders[array_rand($insuranceProviders)],
                    'insurance_number' => 'INS-' . rand(100000, 999999),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function generateMedicalHistory(): string
    {
        $conditions = ['Hypertension', 'Diabetes', 'Asthma', 'Arthritis', 'Migraine'];
        $history = [];

        if (rand(0, 1)) {
            $history[] = $conditions[array_rand($conditions)];
        }

        if (rand(0, 1)) {
            $history[] = 'Previous surgery in ' . (2010 + rand(0, 12));
        }

        return empty($history)
            ? 'No significant medical history'
            : implode(', ', $history);
    }
    private function Phonegenerator(): string
    {
        return '91' . rand(6000000000, 9999999999);
    }
}
