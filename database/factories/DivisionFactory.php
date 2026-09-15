<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => ['en' => $name, 'bn' => $name],
            'description' => ['en' => fake()->sentence(), 'bn' => fake()->sentence()],
            'order' => 0,
        ];
    }
}
