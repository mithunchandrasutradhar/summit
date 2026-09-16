<?php

namespace Database\Factories;

use App\Models\ExhibitorApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => ExhibitorApplication::factory(),
            'company_name' => fake()->unique()->company(),
            'website' => fake()->url(),
            'sector' => fake()->word(),
            'is_published' => true,
        ];
    }
}
