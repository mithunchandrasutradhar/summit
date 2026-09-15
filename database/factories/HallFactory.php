<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class HallFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => 'Hall '.fake()->randomLetter(),
            'capacity' => fake()->numberBetween(50, 500),
        ];
    }
}
