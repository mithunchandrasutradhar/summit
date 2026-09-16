<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'subject',
        'body',
        'available_placeholders',
    ];

    protected function casts(): array
    {
        return [
            'available_placeholders' => 'array',
        ];
    }

    /**
     * Replace {placeholder} tokens with the given values.
     */
    public function render(array $values): array
    {
        $replace = fn (string $text) => preg_replace_callback(
            '/\{(\w+)\}/',
            fn ($m) => $values[$m[1]] ?? $m[0],
            $text
        );

        return [
            'subject' => $replace($this->subject),
            'body' => $replace($this->body),
        ];
    }
}
