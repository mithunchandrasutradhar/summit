<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NewsPostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => ['en' => $title, 'bn' => $title],
            'category' => 'news',
            'excerpt' => ['en' => fake()->sentence(), 'bn' => fake()->sentence()],
            'body' => ['en' => fake()->paragraphs(3, true), 'bn' => fake()->paragraphs(3, true)],
            'is_featured' => false,
            'published_at' => now(),
        ];
    }
}
