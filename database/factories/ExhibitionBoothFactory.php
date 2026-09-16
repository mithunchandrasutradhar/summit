<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitionBoothFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'booth_no' => 'B-'.fake()->unique()->numberBetween(1, 999),
            'zone' => 'Main Hall',
            'size' => '3x3',
            'price' => fake()->randomElement([20000, 35000, 50000]),
            'status' => 'available',
        ];
    }
}
