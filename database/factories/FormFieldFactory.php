<?php

namespace Database\Factories;

use App\Models\FormDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFieldFactory extends Factory
{
    public function definition(): array
    {
        $label = fake()->words(2, true);

        return [
            'form_definition_id' => FormDefinition::factory(),
            'field_key' => str($label)->slug('_'),
            'label' => ['en' => ucfirst($label), 'bn' => ucfirst($label)],
            'type' => 'text',
            'is_required' => false,
            'order' => 0,
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
