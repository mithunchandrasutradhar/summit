<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusProgramFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company().' University';

        return [
            'event_id' => Event::factory(),
            'institution_name' => ['en' => $name, 'bn' => $name],
            'type' => 'university',
            'event_date' => now()->addMonths(1),
            'venue' => fake()->address(),
            'description' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'status' => 'upcoming',
            'order' => 0,
        ];
    }
}
