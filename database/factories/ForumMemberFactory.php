<?php

namespace Database\Factories;

use App\Support\ReferenceNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => ReferenceNumber::generate('forum_members', 'FRM'),
            'email' => fake()->unique()->safeEmail(),
            'consent_accepted_at' => now(),
            'status' => 'submitted',
            'field_values' => [
                'full_name' => fake()->name(),
                'freelancer_category' => fake()->randomElement(['freelancer', 'aspiring', 'agency']),
            ],
        ];
    }
}
