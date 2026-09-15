<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SpeakerFactory extends Factory
{
    public function definition(): array
    {
        $bio = fake()->paragraph();

        return [
            'name' => fake()->name(),
            'designation' => fake()->jobTitle(),
            'organization' => fake()->company(),
            'country' => 'Bangladesh',
            'bio' => ['en' => $bio, 'bn' => $bio],
            'expertise' => [fake()->word(), fake()->word()],
            'social_links' => [],
            'is_featured' => false,
            'is_published' => true,
            'order' => 0,
        ];
    }
}
