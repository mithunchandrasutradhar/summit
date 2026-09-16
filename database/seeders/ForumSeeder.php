<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Faq;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        // Placeholder FAQs — final forum membership rules/copy are pending
        // from BACCO per the brief's content-inputs checklist.
        $faqs = [
            [
                'question' => 'Who can join the BACCO Freelancer Forum?',
                'answer' => 'Freelancers, aspiring freelancers, and agencies working in Bangladesh\'s digital economy are all welcome to apply.',
                'order' => 0,
            ],
            [
                'question' => 'Is there a membership fee?',
                'answer' => 'No, joining the BACCO Freelancer Forum is free.',
                'order' => 1,
            ],
            [
                'question' => 'How long does approval take?',
                'answer' => 'Applications are typically reviewed within a few business days. You\'ll receive an email once a decision is made.',
                'order' => 2,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['group' => 'forum', 'question->en' => $faq['question']],
                [
                    'question' => ['en' => $faq['question'], 'bn' => $faq['question']],
                    'answer' => ['en' => $faq['answer'], 'bn' => $faq['answer']],
                    'order' => $faq['order'],
                    'is_active' => true,
                ]
            );
        }

        $templates = [
            [
                'key' => 'forum_membership_confirmation',
                'subject' => 'Your BACCO Freelancer Forum application has been received',
                'body' => '<p>Hi {name},</p><p>Thanks for applying to join the BACCO Freelancer Forum. Your reference number is <strong>{reference_no}</strong>. We\'ll email you once your application has been reviewed.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'forum_membership_approved',
                'subject' => 'Welcome to the BACCO Freelancer Forum!',
                'body' => '<p>Hi {name},</p><p>Congratulations — your application (<strong>{reference_no}</strong>) has been approved. Welcome to the community!</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
            [
                'key' => 'forum_membership_rejected',
                'subject' => 'Update on your BACCO Freelancer Forum application',
                'body' => '<p>Hi {name},</p><p>Thank you for applying (<strong>{reference_no}</strong>). We\'re unable to approve your application at this time, but you\'re welcome to apply again in the future.</p>',
                'available_placeholders' => ['name', 'reference_no'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(['key' => $template['key']], $template);
        }
    }
}
