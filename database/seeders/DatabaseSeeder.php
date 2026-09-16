<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Event::firstOrCreate(
            ['slug' => 'freelancer-summit-bangladesh-2026'],
            [
                'name' => 'Freelancer Summit Bangladesh 2026',
                'date_start' => '2026-11-26',
                'date_end' => '2026-11-28',
                'venue_name' => null,
                'venue_address' => 'Dhaka, Bangladesh',
                'countdown_target_at' => '2026-11-26 09:00:00',
                'description' => 'The national flagship summit for freelancers, digital professionals and the AI-powered digital economy of Bangladesh.',
                'is_current' => true,
            ]
        );

        foreach ([
            'super_admin',
            'admin',
            'content_editor',
            'sponsorship_manager',
            'exhibition_manager',
            'awards_jury',
        ] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['email' => 'mithun.dev@alpha.net.bd'],
            [
                'name' => 'Mithun',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole('super_admin');

        $this->call(ContentSpineSeeder::class);
        $this->call(NationalJourneySeeder::class);
        $this->call(GrandSummitSeeder::class);
        $this->call(RegistrationEngineSeeder::class);
        $this->call(AwardsSeeder::class);
        $this->call(SponsorshipExhibitionSeeder::class);
        $this->call(ForumSeeder::class);
        $this->call(FormBuilderSeeder::class);

        if (app()->environment('local')) {
            $this->call(DemoContentSeeder::class);
        }
    }
}
