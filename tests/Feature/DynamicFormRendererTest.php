<?php

use App\Livewire\DynamicFormRenderer;
use App\Models\FormDefinition;
use App\Models\FormField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');

    $this->definition = FormDefinition::factory()->create(['key' => 'test_form']);

    FormField::factory()->create([
        'form_definition_id' => $this->definition->id,
        'field_key' => 'name',
        'label' => ['en' => 'Name', 'bn' => 'Name'],
        'type' => 'text',
        'is_required' => true,
        'order' => 0,
    ]);

    FormField::factory()->create([
        'form_definition_id' => $this->definition->id,
        'field_key' => 'email',
        'label' => ['en' => 'Email', 'bn' => 'Email'],
        'type' => 'email',
        'is_required' => true,
        'order' => 1,
        'maps_to_column' => 'email',
    ]);

    FormField::factory()->create([
        'form_definition_id' => $this->definition->id,
        'field_key' => 'consent',
        'label' => ['en' => 'I agree', 'bn' => 'I agree'],
        'type' => 'checkbox',
        'is_required' => true,
        'order' => 2,
        'maps_to_column' => 'consent_accepted_at',
    ]);
});

it('renders the active fields for the given form key', function () {
    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('I agree');
});

it('does not render fields belonging to a different form', function () {
    $otherForm = FormDefinition::factory()->create(['key' => 'other_form']);
    FormField::factory()->create([
        'form_definition_id' => $otherForm->id,
        'field_key' => 'unrelated_field',
        'label' => ['en' => 'Unrelated Field', 'bn' => 'Unrelated Field'],
    ]);

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->assertDontSee('Unrelated Field');
});

it('validates required fields before submitting', function () {
    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', '')
        ->call('submit')
        ->assertHasErrors(['values.name' => 'required']);
});

it('splits submitted values into system columns and custom field_values, and dispatches an event', function () {
    Event::fake();

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', 'Jane Doe')
        ->set('values.email', 'jane@example.com')
        ->set('values.consent', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertDispatched('dynamic-form-submitted', function (string $eventName, array $params) {
            expect($params['formKey'])->toBe('test_form');
            expect($params['system'])->toHaveKey('email', 'jane@example.com');
            expect($params['system'])->toHaveKey('consent_accepted_at');
            expect($params['system']['consent_accepted_at'])->not->toBeNull();
            expect($params['custom'])->toHaveKey('name', 'Jane Doe');
            expect($params['custom'])->not->toHaveKey('email');

            return true;
        });
});

it('rejects submission when a required consent checkbox is left unchecked', function () {
    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', 'Jane Doe')
        ->set('values.email', 'jane@example.com')
        ->set('values.consent', false)
        ->call('submit')
        ->assertHasErrors(['values.consent' => 'accepted']);
});

it('splits a comma-separated tags field into an array custom value', function () {
    FormField::factory()->create([
        'form_definition_id' => $this->definition->id,
        'field_key' => 'interests',
        'label' => ['en' => 'Interests', 'bn' => 'Interests'],
        'type' => 'tags',
        'order' => 3,
    ]);

    Event::fake();

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', 'Jane Doe')
        ->set('values.email', 'jane@example.com')
        ->set('values.consent', true)
        ->set('values.interests', 'AI, Marketplaces , Freelancing')
        ->call('submit')
        ->assertDispatched('dynamic-form-submitted', function (string $eventName, array $params) {
            expect($params['custom']['interests'])->toBe(['AI', 'Marketplaces', 'Freelancing']);

            return true;
        });
});

it('seeds all 5 registration forms with their default fields', function () {
    // The file's beforeEach() already creates one FormDefinition fixture
    // ("test_form") shared by the other tests above, so this only checks
    // that every real key exists — not the total row count.
    (new \Database\Seeders\FormBuilderSeeder())->run();

    foreach (FormDefinition::KEYS as $key) {
        expect(FormDefinition::where('key', $key)->exists())->toBeTrue();
    }

    $registration = FormDefinition::findByKey('summit_registration');
    expect($registration->fields()->count())->toBeGreaterThan(0);
    expect($registration->fields()->where('is_system', true)->count())->toBe($registration->fields()->count());
});

it('stores an uploaded file to pending storage and puts its path in the custom values', function () {
    FormField::factory()->create([
        'form_definition_id' => $this->definition->id,
        'field_key' => 'document',
        'label' => ['en' => 'Document', 'bn' => 'Document'],
        'type' => 'file',
        'order' => 3,
    ]);

    Event::fake();

    $file = UploadedFile::fake()->create('cv.pdf', 100);

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', 'Jane Doe')
        ->set('values.email', 'jane@example.com')
        ->set('values.consent', true)
        ->set('values.document', $file)
        ->call('submit')
        ->assertDispatched('dynamic-form-submitted', function (string $eventName, array $params) {
            expect($params['custom']['document'])->toStartWith('pending-form-uploads/');
            Storage::disk('local')->assertExists($params['custom']['document']);

            return true;
        });
});

it('silently drops a submission when the honeypot field is filled, without dispatching or persisting', function () {
    Event::fake();

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('website', 'https://spam.example.com')
        ->set('values.name', 'Bot')
        ->set('values.email', 'bot@example.com')
        ->set('values.consent', true)
        ->call('submit')
        ->assertSet('submitted', true)
        ->assertNotDispatched('dynamic-form-submitted');
});

it('silently drops a submission that arrives faster than a human could fill the form', function () {
    Event::fake();

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('values.name', 'Bot')
        ->set('values.email', 'bot@example.com')
        ->set('values.consent', true)
        ->call('submit')
        ->assertSet('submitted', true)
        ->assertNotDispatched('dynamic-form-submitted');
});

it('rate-limits repeated submissions from the same connection', function () {
    for ($i = 0; $i < 6; $i++) {
        Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
            ->set('renderedAt', now()->subSeconds(10)->timestamp)
            ->set('values.name', 'Jane Doe')
            ->set('values.email', 'jane@example.com')
            ->set('values.consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('dynamic-form-submitted');
    }

    Livewire::test(DynamicFormRenderer::class, ['formKey' => 'test_form'])
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->set('values.name', 'Jane Doe')
        ->set('values.email', 'jane@example.com')
        ->set('values.consent', true)
        ->call('submit')
        ->assertHasErrors(['submit'])
        ->assertNotDispatched('dynamic-form-submitted');
});
