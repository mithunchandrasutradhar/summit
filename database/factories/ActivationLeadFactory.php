<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivationLeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'activatable_type' => District::class,
            'activatable_id' => District::factory(),
            'type' => 'attendee_interest',
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->sentence(),
        ];
    }
}
