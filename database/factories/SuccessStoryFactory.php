<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SuccessStoryFactory extends Factory
{
    public function definition(): array
    {
        $headline = fake()->sentence(6);

        return [
            'freelancer_name' => fake()->name(),
            'marketplace' => fake()->randomElement(['Upwork', 'Fiverr', 'Freelancer.com']),
            'headline' => ['en' => $headline, 'bn' => $headline],
            'story' => ['en' => fake()->paragraphs(2, true), 'bn' => fake()->paragraphs(2, true)],
            'is_featured' => false,
            'published_at' => now(),
        ];
    }
}
