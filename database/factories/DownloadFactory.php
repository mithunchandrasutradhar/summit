<?php

namespace Database\Factories;

use App\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

class DownloadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(Download::CATEGORIES),
            'is_published' => true,
        ];
    }
}
