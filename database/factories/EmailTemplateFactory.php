<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'subject' => 'Hello {name}',
            'body' => '<p>Thanks {name}, your reference number is {reference_no}.</p>',
            'available_placeholders' => ['name', 'reference_no'],
        ];
    }
}
