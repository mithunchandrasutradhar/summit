<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MediaCoverageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'source_name' => fake()->company(),
            'url' => fake()->url(),
            'published_at' => now(),
        ];
    }
}
