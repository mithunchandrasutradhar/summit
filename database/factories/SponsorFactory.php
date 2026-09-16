<?php

namespace Database\Factories;

use App\Models\SponsorTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'website' => fake()->url(),
            'tier_id' => SponsorTier::factory(),
            'is_published' => true,
            'order' => 0,
        ];
    }
}
