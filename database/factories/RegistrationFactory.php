<?php

namespace Database\Factories;

use App\Models\Event;
use App\Support\ReferenceNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'reference_no' => ReferenceNumber::generate('registrations', 'REG'),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->phoneNumber(),
            'consent_accepted_at' => now(),
            'status' => 'registered',
            'field_values' => ['name' => fake()->name()],
        ];
    }
}
