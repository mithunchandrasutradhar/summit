<?php

namespace Database\Factories;

use App\Models\SponsorshipEnquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payable_type' => SponsorshipEnquiry::class,
            'payable_id' => SponsorshipEnquiry::factory(),
            'amount' => fake()->randomElement([50000, 100000, 250000]),
            'currency' => 'BDT',
            'gateway' => 'sslcommerz',
            'gateway_txn_id' => strtoupper(fake()->bothify('??????-####-??????')),
            'status' => 'pending',
        ];
    }
}
