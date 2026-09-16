<?php

namespace App\Models;

use App\Concerns\HasResponsiveImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Exhibitor extends Model implements HasMedia
{
    use HasFactory, HasResponsiveImages, InteractsWithMedia {
        HasResponsiveImages::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $fillable = [
        'application_id',
        'company_name',
        'website',
        'description',
        'sector',
        'booth_id',
        'is_published',
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

    public function application()
    {
        return $this->belongsTo(ExhibitorApplication::class, 'application_id');
    }

    public function booth()
    {
        return $this->belongsTo(ExhibitionBooth::class, 'booth_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
