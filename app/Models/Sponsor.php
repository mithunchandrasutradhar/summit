<?php

namespace App\Models;

use App\Concerns\HasResponsiveImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sponsor extends Model implements HasMedia
{
    use HasFactory, HasResponsiveImages, InteractsWithMedia {
        HasResponsiveImages::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $fillable = [
        'name',
        'website',
        'tier_id',
        'enquiry_id',
        'is_published',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function logoUrl(string $conversion = ''): ?string
    {
        return $this->getFirstMediaUrl('logo', $conversion) ?: null;
    }

    public function tier()
    {
        return $this->belongsTo(SponsorTier::class, 'tier_id');
    }

    public function enquiry()
    {
        return $this->belongsTo(SponsorshipEnquiry::class, 'enquiry_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
