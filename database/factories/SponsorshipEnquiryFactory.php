<?php

namespace Database\Factories;

use App\Models\SponsorTier;
use App\Support\ReferenceNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorshipEnquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => ReferenceNumber::generate('sponsorship_enquiries', 'SPN'),
            'email' => fake()->unique()->safeEmail(),
            'sponsor_tier_id' => SponsorTier::factory(),
            'status' => 'new',
            'field_values' => [
                'company_name' => fake()->company(),
                'contact_person' => fake()->name(),
                'phone' => fake()->phoneNumber(),
            ],
        ];
    }
}
