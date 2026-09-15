<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class NationalJourneySeeder extends Seeder
{
    public function run(): void
    {
        HomepageSection::firstOrCreate(
            ['section_key' => 'national_journey'],
            ['is_visible' => true, 'order' => 30, 'content' => []]
        );

        // Bangladesh's 8 administrative divisions — perpetual reference data,
        // not edition-scoped, reused across every annual summit.
        foreach ([
            'Dhaka', 'Chattogram', 'Rajshahi', 'Khulna',
            'Barishal', 'Sylhet', 'Rangpur', 'Mymensingh',
        ] as $order => $name) {
            Division::firstOrCreate(
                ['slug' => str($name)->slug()],
                [
                    'name' => ['en' => $name, 'bn' => $name],
                    'order' => $order,
                ]
            );
        }
    }
}
