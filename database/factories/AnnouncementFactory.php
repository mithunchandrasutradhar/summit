<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(),
            'severity' => 'info',
            'is_active' => true,
        ];
    }
}
