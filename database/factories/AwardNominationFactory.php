<?php

namespace Database\Factories;

use App\Models\AwardCategory;
use App\Support\ReferenceNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardNominationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => AwardCategory::factory(),
            'reference_no' => ReferenceNumber::generate('award_nominations', 'AWD'),
            'nominee_email' => fake()->unique()->safeEmail(),
            'declaration_accepted_at' => now(),
            'status' => 'submitted',
            'field_values' => ['nominee_name' => fake()->name()],
        ];
    }
}
