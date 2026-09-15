<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class GrandSummitSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['section_key' => 'featured_speakers', 'order' => 40],
            ['section_key' => 'agenda_preview', 'order' => 50],
        ] as $section) {
            HomepageSection::firstOrCreate(
                ['section_key' => $section['section_key']],
                ['is_visible' => true, 'order' => $section['order'], 'content' => []]
            );
        }
    }
}
