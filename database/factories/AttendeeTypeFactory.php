<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendeeTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Freelancer', 'Student', 'Entrepreneur', 'Company', 'Media']),
            'order' => 0,
        ];
    }
}
