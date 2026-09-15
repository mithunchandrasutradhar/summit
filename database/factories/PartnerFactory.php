<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'website' => fake()->url(),
            'category' => fake()->randomElement(Partner::CATEGORIES),
            'order' => 0,
            'is_published' => true,
        ];
    }
}
