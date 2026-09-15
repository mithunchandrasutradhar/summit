<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignStat extends Model
{
    use HasFactory;

    public const KEYS = [
        'divisions_covered',
        'districts_covered',
        'institutions_activated',
        'participants_reached',
    ];

    protected $fillable = [
        'event_id',
        'key',
        'value',
        'is_manual_override',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'is_manual_override' => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
