<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
        ];
    }
}
