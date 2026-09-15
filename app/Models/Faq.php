<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasFactory, HasTranslations;

    public const GROUPS = [
        'general',
        'forum',
        'awards',
        'sponsorship',
        'exhibition',
    ];

    public array $translatable = ['question', 'answer'];

    protected $fillable = [
        'group',
        'question',
        'answer',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
