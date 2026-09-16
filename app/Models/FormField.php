<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FormField extends Model
{
    use HasFactory, HasTranslations;

    public const TYPES = [
        'text', 'email', 'tel', 'textarea', 'select', 'checkbox',
        'radio', 'date', 'file', 'tags', 'url', 'relation_select',
    ];

    public array $translatable = ['label'];

    protected $fillable = [
        'form_definition_id',
        'field_key',
        'label',
        'type',
        'options',
        'relation_source',
        'is_required',
        'validation_rules',
        'placeholder',
        'help_text',
        'order',
        'is_system',
        'maps_to_column',
        'is_filterable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation_rules' => 'array',
            'is_required' => 'boolean',
            'is_system' => 'boolean',
            'is_filterable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function formDefinition()
    {
        return $this->belongsTo(FormDefinition::class);
    }

    /**
     * Build the Laravel validation rule string for this field.
     */
    public function buildRules(): array
    {
        // A required checkbox (e.g. a consent/declaration) must actually be
        // checked — plain "required" treats `false` as present, so it would
        // let an unchecked box through. "accepted" is the rule that means
        // "must be true/1/yes/on".
        if ($this->type === 'checkbox') {
            $rules = $this->is_required ? ['accepted'] : ['boolean'];

            return array_merge($rules, $this->validation_rules ?? []);
        }

        $rules = [$this->is_required ? 'required' : 'nullable'];

        $rules[] = match ($this->type) {
            'email' => 'email',
            'url' => 'url',
            'date' => 'date',
            'file' => 'file',
            // 'tags' arrives from the form as a raw comma-separated string
            // and is only split into an array after validation passes.
            default => 'string',
        };

        if ($this->type === 'select' || $this->type === 'radio') {
            $options = array_keys($this->options ?? []);
            if ($options !== []) {
                $rules[] = 'in:'.implode(',', $options);
            }
        }

        $custom = $this->validation_rules ?? [];

        if ($this->type === 'file') {
            // Sensible defaults so an admin-added file field isn't wide open
            // to arbitrary uploads by default — skipped if the admin has
            // already set their own mime/size rule for this field.
            $hasCustomMimes = collect($custom)->contains(fn ($rule) => is_string($rule) && str_starts_with($rule, 'mimes:'));
            $hasCustomSize = collect($custom)->contains(fn ($rule) => is_string($rule) && (str_starts_with($rule, 'max:') || str_starts_with($rule, 'size:')));

            if (! $hasCustomMimes) {
                $rules[] = 'mimes:pdf,jpg,jpeg,png,doc,docx';
            }

            if (! $hasCustomSize) {
                $rules[] = 'max:5120'; // 5MB
            }
        }

        return array_merge($rules, $custom);
    }

    /**
     * Whether this field's submitted value should be written to a real
     * column on the eventual submission model, rather than field_values.
     */
    public function mapsToRealColumn(): bool
    {
        return ! empty($this->maps_to_column);
    }
}
