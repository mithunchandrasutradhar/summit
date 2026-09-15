<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    public function definition(): array
    {
        $question = fake()->sentence().'?';

        return [
            'group' => 'general',
            'question' => ['en' => $question, 'bn' => $question],
            'answer' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'order' => 0,
            'is_active' => true,
        ];
    }
}
