<?php

namespace Database\Factories;

use App\Models\AwardNomination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardNominationReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nomination_id' => AwardNomination::factory(),
            'reviewer_id' => User::factory(),
            'decision' => 'no_decision',
        ];
    }
}
