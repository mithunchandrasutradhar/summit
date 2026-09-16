<?php

namespace Database\Seeders;

use App\Models\AwardCategory;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class AwardsSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSection::firstOrCreate(
            ['section_key' => 'awards_spotlight'],
            ['is_visible' => true, 'order' => 60, 'content' => []]
        );

        $event = Event::current();

        if ($event) {
            // Placeholder category so the nomination form isn't empty out of
            // the box — final categories/eligibility/criteria are pending
            // from BACCO per the brief's content-inputs checklist.
            $name = 'Freelancer of the Year';
            AwardCategory::firstOrCreate(
                ['slug' => str($name)->slug(), 'event_id' => $event->id],
                [
                    'name' => ['en' => $name, 'bn' => $name],
                    'description' => ['en' => 'Placeholder category — final award categories are pending from the organizing team.', 'bn' => 'Placeholder category — final award categories are pending from the organizing team.'],
                    'is_active' => true,
                    'order' => 0,
                ]
            );
        }

        $templates = [
            [
                'key' => 'award_nomination_confirmation',
                'subject' => 'Your Freelancer Award nomination has been received',
                'body' => '<p>Hi {name},</p><p>Thanks for your nomination. Your reference number is <strong>{reference_no}</strong>. We\'ll be in touch as the review process progresses.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'award_under_review',
                'subject' => 'Your Freelancer Award nomination is under review',
                'body' => '<p>Hi {name},</p><p>Your nomination (<strong>{reference_no}</strong>) is now being reviewed by our jury panel. We\'ll let you know the outcome soon.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'award_shortlisted',
                'subject' => 'You\'ve been shortlisted for a Freelancer Award!',
                'body' => '<p>Hi {name},</p><p>Congratulations — your nomination (<strong>{reference_no}</strong>) has been shortlisted. Winners will be announced at the Grand Summit.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'award_winner',
                'subject' => 'Congratulations — you\'ve won a Freelancer Award!',
                'body' => '<p>Hi {name},</p><p>Congratulations! Your nomination (<strong>{reference_no}</strong>) has been selected as a winner. We\'ll contact you with details.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'award_rejected',
                'subject' => 'Update on your Freelancer Award nomination',
                'body' => '<p>Hi {name},</p><p>Thank you for your nomination (<strong>{reference_no}</strong>). This year it was not selected to move forward, but we encourage you to apply again next year.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'award_incomplete',
                'subject' => 'Action needed on your Freelancer Award nomination',
                'body' => '<p>Hi {name},</p><p>Your nomination (<strong>{reference_no}</strong>) is missing some required information and can\'t be reviewed yet. Please contact us so we can help complete it.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(['key' => $template['key']], $template);
        }
    }
}
