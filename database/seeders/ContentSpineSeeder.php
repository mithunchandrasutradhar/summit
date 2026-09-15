<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ContentSpineSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['section_key' => 'hero', 'order' => 0],
            ['section_key' => 'trust_strip', 'order' => 10],
            ['section_key' => 'positioning', 'order' => 20],
            ['section_key' => 'success_stories', 'order' => 90],
            ['section_key' => 'partners_sponsors', 'order' => 100],
            ['section_key' => 'latest_news', 'order' => 110],
            ['section_key' => 'final_cta', 'order' => 120],
        ];

        foreach ($sections as $section) {
            HomepageSection::firstOrCreate(
                ['section_key' => $section['section_key']],
                ['is_visible' => true, 'order' => $section['order'], 'content' => []]
            );
        }

        $placeholderPages = [
            'about' => [
                'title' => 'About Summit',
                'body' => '<p>Content for this page is pending from the organizing team (BACCO/DoICT). Placeholder text — replace via the admin panel.</p>',
            ],
            'contact' => [
                'title' => 'Contact',
                'body' => '<p>Official contact details are pending from the organizing team. Replace this placeholder via the admin panel.</p>',
            ],
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'body' => '<p>Placeholder — final privacy policy wording is pending from the organizing team/legal counsel.</p>',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'body' => '<p>Placeholder — final terms & conditions wording is pending from the organizing team/legal counsel.</p>',
            ],
        ];

        foreach ($placeholderPages as $slug => $data) {
            Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['en' => $data['title'], 'bn' => $data['title']],
                    'body' => ['en' => $data['body'], 'bn' => $data['body']],
                    'is_published' => true,
                ]
            );
        }
    }
}
