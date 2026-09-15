<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Freelancer Summit Bangladesh '.fake()->year(),
            'date_start' => now()->addMonths(6),
            'date_end' => now()->addMonths(6)->addDays(2),
            'venue_address' => 'Dhaka, Bangladesh',
            'countdown_target_at' => now()->addMonths(6),
            'description' => fake()->paragraph(),
            'is_current' => false,
        ];
    }
}
