<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ExhibitionBooth extends Model
{
    use HasFactory;

    public const STATUSES = ['available', 'reserved', 'confirmed'];

    protected $fillable = [
        'event_id',
        'booth_no',
        'zone',
        'size',
        'price',
        'status',
        'map_x',
        'map_y',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Booths have no "name" column, but the shared relation_select dropdown
     * partial (dynamic-form-renderer.blade.php) falls back to `->name` for
     * its option label, so expose one here instead of touching that view.
     */
    public function getNameAttribute(): string
    {
        return 'Booth '.$this->booth_no.($this->zone ? ' — '.$this->zone : '');
    }

    public function applications()
    {
        return $this->hasMany(ExhibitorApplication::class, 'preferred_booth_id');
    }

    public function exhibitor()
    {
        return $this->hasOne(Exhibitor::class, 'booth_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeForCurrentEvent($query)
    {
        return $query->whereHas('event', fn ($q) => $q->where('is_current', true));
    }

    /**
     * Convention used by DynamicFormRenderer's relation_select fields — see
     * AttendeeType::selectableOptions() for why this exists.
     */
    public static function selectableOptions(): Collection
    {
        return static::available()->forCurrentEvent()->orderBy('booth_no')->get();
    }
}
