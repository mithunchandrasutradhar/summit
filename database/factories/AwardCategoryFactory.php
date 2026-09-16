<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true).' Award';

        return [
            'event_id' => Event::factory(),
            'name' => ['en' => $name, 'bn' => $name],
            'description' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'eligibility' => ['en' => fake()->sentence(), 'bn' => fake()->sentence()],
            'criteria' => ['en' => fake()->sentence(), 'bn' => fake()->sentence()],
            'submission_deadline' => now()->addMonths(2),
            'is_active' => true,
            'order' => 0,
        ];
    }
}
