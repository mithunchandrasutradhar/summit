<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorTierFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true).' Tier';

        return [
            'event_id' => Event::factory(),
            'name' => ['en' => $name, 'bn' => $name],
            'price' => fake()->randomElement([50000, 100000, 250000, 500000]),
            'benefits' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'order' => 0,
            'is_active' => true,
        ];
    }
}
