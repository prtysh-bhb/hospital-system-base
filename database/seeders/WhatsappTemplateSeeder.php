<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WhatsappTemplate;

class WhatsappTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'id' => 'APPOINTMENT_CONFIRM',
                'name' => 'Appointment Confirm',
                'message' => <<<EOD
                Hello {{name}}, your appointment is confirmed for {{date}} at {{time}}.
                EOD
            ],
            [
                'id' => 'APPOINTMENT_CANCEL',
                'name' => 'Appointment Cancel',
                'message' => <<<EOD
                Hello {{name}}, 

                We regret to inform you that your appointment has been cancelled due to {{cancellation_reason}}. If you wish to reschedule, please contact us at your earliest convenience.

                Thank you for your understanding.
                EOD
            ],
            [
                'id' => 'APPOINTMENT_RESCHEDULE',
                'name' => 'Appointment Reschedule',
                'message' => <<<EOD
                Hello {{name}}, your appointment is rescheduled to {{date}} at {{time}}.
                EOD
            ],
            [
                'id' => 'APPOINTMENT_REMINDER',
                'name' => 'Appointment Reminder',
                'message' => <<<EOD
                Reminder: your appointment is on {{date}} at {{time}}.
                EOD
            ],
            [
                'id' => 'APPOINTMENT_COMPLETED',
                'name' => 'Appointment Completed',
                'message' => <<<EOD
                Thank you {{name}}, your appointment is completed.
                EOD
            ],
            [
                'id' => 'DOCTOR_ON_LEAVE',
                'name' => 'Doctor On Leave',
                'message' => <<<EOD
                👋 Hello {{name}}, 

                We wanted to inform you that Dr. {{doctor_name}} will be on leave from {{start_date}} to {{end_date}} and will not be available for appointments during this period.

                If you wish to book another appointment, please click the link below to schedule at a convenient time:
                {{booking_link}}

                Thank you for your understanding.
                EOD
            ]
        ];


        foreach ($templates as $template) {
            WhatsappTemplate::updateOrCreate(
                ['id' => $template['id']],
                $template
            );
        }
    }
}
