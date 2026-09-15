<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'event_id' => Event::factory(),
            'title' => ['en' => $title, 'bn' => $title],
            'description' => ['en' => fake()->paragraph(), 'bn' => fake()->paragraph()],
            'type' => fake()->randomElement(Session::TYPES),
            'date' => now()->addMonths(3),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'is_published' => true,
            'order' => 0,
        ];
    }
}
