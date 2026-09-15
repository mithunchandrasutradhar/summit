<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'section_key',
        'is_visible',
        'order',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'content' => 'array',
        ];
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)->orderBy('order');
    }
}
