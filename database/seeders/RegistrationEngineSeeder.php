<?php

namespace Database\Seeders;

use App\Models\AttendeeType;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class RegistrationEngineSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Freelancer', 'Aspiring Freelancer', 'Student', 'Entrepreneur', 'Company', 'Media'] as $order => $name) {
            AttendeeType::firstOrCreate(['name' => $name], ['order' => $order]);
        }

        $templates = [
            [
                'key' => 'registration_confirmation',
                'subject' => 'You\'re registered for Freelancer Summit Bangladesh 2026!',
                'body' => '<p>Hi {name},</p><p>Thanks for registering for Freelancer Summit Bangladesh 2026. Your reference number is <strong>{reference_no}</strong>.</p><p>Bring your QR e-ticket (shown on the confirmation page) to the gate for check-in.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'registration_reminder',
                'subject' => 'The summit is coming up — see you there!',
                'body' => '<p>Hi {name},</p><p>Just a reminder that Freelancer Summit Bangladesh 2026 is coming up soon. Your reference number is <strong>{reference_no}</strong>.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(['key' => $template['key']], $template);
        }
    }
}
