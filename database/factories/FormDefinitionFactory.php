<?php

namespace Database\Factories;

use App\Models\FormDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormDefinitionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->randomElement(FormDefinition::KEYS),
            'name' => fake()->words(3, true),
        ];
    }
}
