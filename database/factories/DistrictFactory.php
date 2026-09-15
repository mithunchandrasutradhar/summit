<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'division_id' => Division::factory(),
            'event_id' => Event::factory(),
            'name' => ['en' => $name, 'bn' => $name],
            'description' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'venue' => fake()->address(),
            'event_date' => now()->addMonths(2),
            'status' => 'upcoming',
            'participants_count' => fake()->numberBetween(50, 500),
            'order' => 0,
        ];
    }
}
