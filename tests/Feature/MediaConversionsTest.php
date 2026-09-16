<?php

use App\Models\Partner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('generates responsive thumb, medium and large conversions for an uploaded logo, all re-encoded to WebP', function () {
    Storage::fake('public');

    $partner = Partner::factory()->create();

    $partner->addMedia(UploadedFile::fake()->image('logo.jpg', 2200, 1400))
        ->toMediaCollection('logo');

    $media = $partner->getFirstMedia('logo');

    expect($media)->not->toBeNull();
    expect($media->hasGeneratedConversion('thumb'))->toBeTrue();
    expect($media->hasGeneratedConversion('medium'))->toBeTrue();
    expect($media->hasGeneratedConversion('large'))->toBeTrue();
    expect($partner->logoUrl('thumb'))->not->toBeNull();
    expect($partner->logoUrl('thumb'))->not->toBe($partner->logoUrl());

    expect($partner->logoUrl('thumb'))->toEndWith('.webp');
    expect($partner->logoUrl('medium'))->toEndWith('.webp');
    expect($partner->logoUrl('large'))->toEndWith('.webp');
});
