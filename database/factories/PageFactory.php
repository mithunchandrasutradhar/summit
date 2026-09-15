<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => ['en' => $title, 'bn' => $title],
            'body' => ['en' => fake()->paragraphs(3, true), 'bn' => fake()->paragraphs(3, true)],
            'is_published' => true,
        ];
    }
}
