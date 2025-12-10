<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $specialties = [
            ['name' => 'Cardiology', 'description' => 'Heart and cardiovascular system specialists', 'icon' => 'heart', 'status' => 'active'],
            ['name' => 'Pediatrics', 'description' => 'Children\'s health and development', 'icon' => 'child', 'status' => 'active'],
            ['name' => 'Orthopedics', 'description' => 'Bones, joints, and musculoskeletal system', 'icon' => 'bone', 'status' => 'active'],
            ['name' => 'Dermatology', 'description' => 'Skin, hair, and nail conditions', 'icon' => 'skin', 'status' => 'active'],
            ['name' => 'Neurology', 'description' => 'Nervous system and brain disorders', 'icon' => 'brain', 'status' => 'active'],
            ['name' => 'Gynecology', 'description' => 'Women\'s reproductive health', 'icon' => 'female', 'status' => 'active'],
            ['name' => 'Dentistry', 'description' => 'Oral health and dental care', 'icon' => 'tooth', 'status' => 'active'],
            ['name' => 'Psychiatry', 'description' => 'Mental health and behavioral disorders', 'icon' => 'mind', 'status' => 'active'],

            ['name' => 'Oncology', 'description' => 'Cancer diagnosis and treatment', 'icon' => 'cancer', 'status' => 'active'],
            ['name' => 'Radiology', 'description' => 'Medical imaging and scans', 'icon' => 'xray', 'status' => 'active'],
            ['name' => 'Anesthesiology', 'description' => 'Anesthesia and perioperative care', 'icon' => 'anesthesia', 'status' => 'active'],
            ['name' => 'Urology', 'description' => 'Urinary tract and male reproductive system', 'icon' => 'urology', 'status' => 'active'],
            ['name' => 'Nephrology', 'description' => 'Kidney-related diseases', 'icon' => 'kidney', 'status' => 'active'],
            ['name' => 'Gastroenterology', 'description' => 'Digestive system and stomach issues', 'icon' => 'stomach', 'status' => 'active'],
            ['name' => 'Endocrinology', 'description' => 'Hormonal disorders and metabolism', 'icon' => 'hormone', 'status' => 'active'],
            ['name' => 'Rheumatology', 'description' => 'Arthritis and autoimmune diseases', 'icon' => 'joint', 'status' => 'active'],
            ['name' => 'Pulmonology', 'description' => 'Lungs and respiratory system', 'icon' => 'lung', 'status' => 'active'],
            ['name' => 'Ophthalmology', 'description' => 'Eye and vision care', 'icon' => 'eye', 'status' => 'active'],
            ['name' => 'ENT', 'description' => 'Ear, nose, and throat disorders', 'icon' => 'ear', 'status' => 'active'],
            ['name' => 'General Surgery', 'description' => 'General surgical treatments', 'icon' => 'scalpel', 'status' => 'active'],
            ['name' => 'Plastic Surgery', 'description' => 'Cosmetic and reconstructive surgery', 'icon' => 'plastic-surgery', 'status' => 'active'],
            ['name' => 'Vascular Surgery', 'description' => 'Blood vessel surgery', 'icon' => 'veins', 'status' => 'active'],
            ['name' => 'Neurosurgery', 'description' => 'Brain and nervous system surgery', 'icon' => 'neuro-surgery', 'status' => 'active'],
            ['name' => 'Emergency Medicine', 'description' => 'Emergency and critical care', 'icon' => 'emergency', 'status' => 'active'],
            ['name' => 'Internal Medicine', 'description' => 'Adult diseases and diagnosis', 'icon' => 'internal', 'status' => 'active'],
            ['name' => 'Family Medicine', 'description' => 'Primary care for all ages', 'icon' => 'family', 'status' => 'active'],
            ['name' => 'Sexology', 'description' => 'Sexual health and wellness', 'icon' => 'sex', 'status' => 'active'],
            ['name' => 'Hematology', 'description' => 'Blood diseases and disorders', 'icon' => 'blood', 'status' => 'active'],
            ['name' => 'Infectious Disease', 'description' => 'Viral and bacterial infections', 'icon' => 'infection', 'status' => 'active'],
            ['name' => 'Allergy & Immunology', 'description' => 'Allergy and immune system disorders', 'icon' => 'allergy', 'status' => 'active'],
            ['name' => 'Sports Medicine', 'description' => 'Sports injury and fitness medicine', 'icon' => 'sports', 'status' => 'active'],
            ['name' => 'Geriatrics', 'description' => 'Health care of elderly', 'icon' => 'old-age', 'status' => 'active'],
            ['name' => 'Pain Management', 'description' => 'Chronic pain treatment', 'icon' => 'pain', 'status' => 'active'],
            ['name' => 'Critical Care', 'description' => 'Life-threatening condition management', 'icon' => 'icu', 'status' => 'active'],
            ['name' => 'Sleep Medicine', 'description' => 'Sleep-related disorders', 'icon' => 'sleep', 'status' => 'active'],
            ['name' => 'Palliative Care', 'description' => 'End-of-life and supportive care', 'icon' => 'care', 'status' => 'active'],
            ['name' => 'Rehabilitation Medicine', 'description' => 'Recovery & physical rehab', 'icon' => 'rehab', 'status' => 'active'],
            ['name' => 'Nuclear Medicine', 'description' => 'Radiation-based diagnosis', 'icon' => 'nuclear', 'status' => 'active'],
            ['name' => 'Oral Surgery', 'description' => 'Dental surgeries', 'icon' => 'oral', 'status' => 'active'],
            ['name' => 'Chiropractic', 'description' => 'Spine and muscular adjustments', 'icon' => 'chiropractic', 'status' => 'active'],
            ['name' => 'Nutrition', 'description' => 'Diet and nutrition sciences', 'icon' => 'nutrition', 'status' => 'active'],
            ['name' => 'Physiotherapy', 'description' => 'Physical therapy treatments', 'icon' => 'physio', 'status' => 'active'],
            ['name' => 'Speech Therapy', 'description' => 'Speech and communication disorders', 'icon' => 'speech', 'status' => 'active'],
            ['name' => 'Occupational Therapy', 'description' => 'Daily activity rehabilitation', 'icon' => 'occupation', 'status' => 'active'],
            ['name' => 'Audiology', 'description' => 'Hearing and balance disorders', 'icon' => 'hearing', 'status' => 'active'],
            ['name' => 'Cosmetology', 'description' => 'Beauty & skin treatments', 'icon' => 'cosmetic', 'status' => 'active'],
        ];

        foreach ($specialties as $spec) {
            DB::table('specialties')->updateOrInsert(
                ['name' => $spec['name']],  // WHERE condition
                [
                    'description' => $spec['description'],
                    'icon' => $spec['icon'],
                    'status' => $spec['status'],
                    'updated_at' => now(),
                    'created_at' => now(), // created_at ignored if row exists
                ]
            );
        }
    }
}
