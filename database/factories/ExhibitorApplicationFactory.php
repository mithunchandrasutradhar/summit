<?php

namespace Database\Factories;

use App\Models\ExhibitionBooth;
use App\Support\ReferenceNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitorApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => ReferenceNumber::generate('exhibitor_applications', 'EXH'),
            'email' => fake()->unique()->safeEmail(),
            'preferred_booth_id' => ExhibitionBooth::factory(),
            'status' => 'new',
            'field_values' => [
                'organization_name' => fake()->company(),
                'contact_person' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'sector' => fake()->word(),
            ],
        ];
    }
}
