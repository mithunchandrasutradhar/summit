<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use App\Models\HomepageSection;
use App\Models\SponsorTier;
use Illuminate\Database\Seeder;

class SponsorshipExhibitionSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSection::firstOrCreate(
            ['section_key' => 'sponsors_strip'],
            ['is_visible' => true, 'order' => 70, 'content' => []]
        );

        $event = Event::current();

        if ($event) {
            // Placeholder tiers — final sponsorship package pricing and
            // benefits are pending from BACCO per the brief's content-inputs
            // checklist.
            foreach ([
                ['name' => 'Title Sponsor', 'order' => 0],
                ['name' => 'Platinum Sponsor', 'order' => 1],
                ['name' => 'Gold Sponsor', 'order' => 2],
                ['name' => 'Silver Sponsor', 'order' => 3],
            ] as $tier) {
                SponsorTier::firstOrCreate(
                    ['slug' => str($tier['name'])->slug(), 'event_id' => $event->id],
                    [
                        'name' => ['en' => $tier['name'], 'bn' => $tier['name']],
                        'price' => 0,
                        'benefits' => [
                            'en' => 'Benefits and pricing to be finalized by the organizing team.',
                            'bn' => 'Benefits and pricing to be finalized by the organizing team.',
                        ],
                        'order' => $tier['order'],
                        'is_active' => true,
                    ]
                );
            }

            // Placeholder booth layout — final floor plan and pricing are
            // pending from BACCO.
            for ($i = 1; $i <= 10; $i++) {
                ExhibitionBooth::firstOrCreate(
                    ['event_id' => $event->id, 'booth_no' => 'B-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)],
                    [
                        'zone' => 'Main Hall',
                        'size' => '3x3',
                        'price' => 0,
                        'status' => 'available',
                    ]
                );
            }
        }

        $templates = [
            [
                'key' => 'sponsorship_enquiry_confirmation',
                'subject' => 'We\'ve received your sponsorship enquiry',
                'body' => '<p>Hi {name},</p><p>Thanks for your interest in sponsoring Freelancer Summit Bangladesh 2026. Your reference number is <strong>{reference_no}</strong>. Our team will be in touch shortly.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'sponsorship_payment_link',
                'subject' => 'Complete your sponsorship payment',
                'body' => '<p>Hi {name},</p><p>Thank you for confirming your sponsorship (<strong>{reference_no}</strong>). Please complete payment using the secure link below:</p><p><a href="{payment_link}">{payment_link}</a></p>',
                'available_placeholders' => ['name', 'reference_no', 'payment_link'],
            ],
            [
                'key' => 'sponsorship_confirmed',
                'subject' => 'Your sponsorship is confirmed!',
                'body' => '<p>Hi {name},</p><p>Your payment has been received and your sponsorship (<strong>{reference_no}</strong>) is now confirmed. Welcome aboard!</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'exhibitor_application_confirmation',
                'subject' => 'We\'ve received your exhibition booth application',
                'body' => '<p>Hi {name},</p><p>Thanks for applying for a booth at Freelancer Summit Bangladesh 2026. Your reference number is <strong>{reference_no}</strong>. Our team will be in touch shortly.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'exhibition_payment_link',
                'subject' => 'Complete your exhibition booth payment',
                'body' => '<p>Hi {name},</p><p>Thank you for confirming your booth (<strong>{reference_no}</strong>). Please complete payment using the secure link below:</p><p><a href="{payment_link}">{payment_link}</a></p>',
                'available_placeholders' => ['name', 'reference_no', 'payment_link'],
            ],
            [
                'key' => 'exhibitor_confirmed',
                'subject' => 'Your exhibition booth is confirmed!',
                'body' => '<p>Hi {name},</p><p>Your payment has been received and your booth (<strong>{reference_no}</strong>) is now confirmed. We look forward to having you at the summit!</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'sponsorship_enquiry_new_lead_internal',
                'subject' => 'New sponsorship enquiry: {reference_no}',
                'body' => '<p>A new sponsorship enquiry has been submitted.</p><p><strong>Company:</strong> {company_name}<br><strong>Contact:</strong> {contact_person}<br><strong>Reference:</strong> {reference_no}</p><p><a href="{admin_url}">View in admin</a></p>',
                'available_placeholders' => ['company_name', 'contact_person', 'reference_no', 'admin_url'],
            ],
            [
                'key' => 'exhibitor_application_new_lead_internal',
                'subject' => 'New exhibitor application: {reference_no}',
                'body' => '<p>A new exhibitor application has been submitted.</p><p><strong>Organization:</strong> {organization_name}<br><strong>Contact:</strong> {contact_person}<br><strong>Reference:</strong> {reference_no}</p><p><a href="{admin_url}">View in admin</a></p>',
                'available_placeholders' => ['organization_name', 'contact_person', 'reference_no', 'admin_url'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(['key' => $template['key']], $template);
        }
    }
}
