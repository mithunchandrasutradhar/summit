<?php

use App\Models\FormField;

it('builds required/nullable plus the type-appropriate rule for ordinary field types', function () {
    $required = new FormField(['type' => 'email', 'is_required' => true]);
    expect($required->buildRules())->toBe(['required', 'email']);

    $optional = new FormField(['type' => 'url', 'is_required' => false]);
    expect($optional->buildRules())->toBe(['nullable', 'url']);
});

it('uses "accepted" for a required checkbox instead of plain "required"', function () {
    $consent = new FormField(['type' => 'checkbox', 'is_required' => true]);
    expect($consent->buildRules())->toBe(['accepted']);

    $optionalCheckbox = new FormField(['type' => 'checkbox', 'is_required' => false]);
    expect($optionalCheckbox->buildRules())->toBe(['boolean']);
});

it('restricts select/radio fields to their configured option keys', function () {
    $field = new FormField([
        'type' => 'select',
        'is_required' => true,
        'options' => ['a' => 'Option A', 'b' => 'Option B'],
    ]);

    expect($field->buildRules())->toBe(['required', 'string', 'in:a,b']);
});

it('applies default mime and size limits to a file field with no custom rules', function () {
    $field = new FormField(['type' => 'file', 'is_required' => false]);

    expect($field->buildRules())->toBe(['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120']);
});

it('does not override an admin-specified mime or size rule on a file field', function () {
    $field = new FormField([
        'type' => 'file',
        'is_required' => false,
        'validation_rules' => ['mimes:pdf', 'max:2048'],
    ]);

    $rules = $field->buildRules();

    expect($rules)->toBe(['nullable', 'file', 'mimes:pdf', 'max:2048']);
});

it('merges arbitrary custom validation_rules onto any field type', function () {
    $field = new FormField([
        'type' => 'text',
        'is_required' => true,
        'validation_rules' => ['max:255', 'alpha_dash'],
    ]);

    expect($field->buildRules())->toBe(['required', 'string', 'max:255', 'alpha_dash']);
});
