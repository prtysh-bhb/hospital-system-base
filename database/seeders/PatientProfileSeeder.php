<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $patients = DB::table('users')->where('role', 'patient')->get();

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $allergies = ['Penicillin', 'Aspirin', 'Dust', 'Pollen', 'Shellfish', 'Latex', 'None'];
        $medications = ['Metformin', 'Insulin', 'Lisinopril', 'Atorvastatin', 'Levothyroxine', 'None'];
        $insuranceProviders = ['MediHealth', 'CarePlus', 'HealthGuard', 'SecureLife', 'Wellness Inc'];

        $patientProfiles = [];

        foreach ($patients as $patient) {
            $hasAllergies = rand(0, 1);
            $hasMeds = rand(0, 1);

            $patientProfiles[] = [
                'user_id' => $patient->id,
                'emergency_contact_name' => 'Emergency Contact '.$patient->last_name,
                'emergency_contact_phone' => '+1-555-EMER-'.rand(100, 999),
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'allergies' => $hasAllergies ? $allergies[array_rand($allergies)] : 'None',
                'medical_history' => $this->generateMedicalHistory(),
                'current_medications' => $hasMeds ? $medications[array_rand($medications)] : 'None',
                'insurance_provider' => $insuranceProviders[array_rand($insuranceProviders)],
                'insurance_number' => 'INS-'.rand(100000, 999999),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('patient_profiles')->insert($patientProfiles);
    }

    private function generateMedicalHistory()
    {
        $conditions = ['Hypertension', 'Diabetes', 'Asthma', 'Arthritis', 'Migraine'];
        $history = [];

        if (rand(0, 1)) {
            $history[] = $conditions[array_rand($conditions)];
        }

        if (rand(0, 1)) {
            $history[] = 'Previous surgery in '.(2010 + rand(0, 12));
        }

        return empty($history) ? 'No significant medical history' : implode(', ', $history);
    }
}
