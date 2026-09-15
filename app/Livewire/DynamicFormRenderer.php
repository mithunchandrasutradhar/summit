<?php

namespace App\Livewire;

use App\Models\FormDefinition;
use App\Models\FormField;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Renders and validates any of the app's registration-style forms from
 * their admin-configured field list, then hands the collected values off
 * to whichever parent component actually persists them — this component
 * has no knowledge of registrations/nominations/enquiries/etc. Consumers
 * listen for the `dynamic-form-submitted` event.
 *
 * Each field's submitted value goes to one of two buckets:
 *  - `system`: keyed by the field's `maps_to_column`, for fields whose
 *    value belongs on a real column on the eventual submission model
 *    (e.g. email, mobile, a consent checkbox mapped to a timestamp).
 *  - `custom`: keyed by `field_key`, for everything else — stored in
 *    that model's `field_values` JSON column.
 */
class DynamicFormRenderer extends Component
{
    use WithFileUploads;

    public string $formKey;

    public array $values = [];

    public bool $submitted = false;

    public function mount(string $formKey): void
    {
        $this->formKey = $formKey;

        foreach ($this->fields() as $field) {
            $this->values[$field->field_key] = $field->type === 'checkbox' ? false : null;
        }
    }

    /** @return Collection<int, FormField> */
    protected function fields(): Collection
    {
        $definition = FormDefinition::findByKey($this->formKey);

        if (! $definition) {
            return collect();
        }

        return $definition->fields()->where('is_active', true)->get();
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->fields() as $field) {
            $rules['values.'.$field->field_key] = $field->buildRules();
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        return $this->fields()->mapWithKeys(
            fn (FormField $field) => ['values.'.$field->field_key => $field->label]
        )->all();
    }

    public function submit(): void
    {
        $this->validate();

        $system = [];
        $custom = [];

        foreach ($this->fields() as $field) {
            $value = $this->values[$field->field_key] ?? null;

            if ($field->type === 'file' && $value) {
                $value = $this->storePendingUpload($value);
            }

            if ($field->type === 'tags' && is_string($value)) {
                $value = collect(explode(',', $value))->map(fn ($v) => trim($v))->filter()->values()->all();
            }

            if ($field->mapsToRealColumn()) {
                if ($field->type === 'checkbox' && str($field->maps_to_column)->endsWith('_at')) {
                    $value = $value ? now()->toDateTimeString() : null;
                }

                $system[$field->maps_to_column] = $value;

                continue;
            }

            $custom[$field->field_key] = $value;
        }

        $this->dispatch('dynamic-form-submitted', formKey: $this->formKey, system: $system, custom: $custom);

        $this->reset('values');
        foreach ($this->fields() as $field) {
            $this->values[$field->field_key] = $field->type === 'checkbox' ? false : null;
        }

        $this->submitted = true;
    }

    /**
     * Move a Livewire temporary upload into permanent-but-unclaimed storage
     * and return its relative path. The parent component that ultimately
     * creates the submission record is responsible for attaching this file
     * via the media library and deleting it from pending storage.
     */
    protected function storePendingUpload($file): string
    {
        return $file->store('pending-form-uploads', 'local');
    }

    public function render()
    {
        return view('livewire.dynamic-form-renderer', [
            'fields' => $this->fields(),
        ]);
    }
}
