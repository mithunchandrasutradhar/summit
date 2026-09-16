<?php

use App\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('serves a responsive srcset (not the raw original) on the media gallery index page', function () {
    Storage::fake('public');

    $gallery = Gallery::factory()->create();
    $gallery->addMedia(UploadedFile::fake()->image('cover.jpg', 2400, 1600))->toMediaCollection('photos');

    $response = $this->get('/en/media')->assertOk();

    $response->assertSee('srcset=', false);
    // The original, un-converted file must not be what's served as the cover.
    $originalUrl = $gallery->getFirstMedia('photos')->getUrl();
    $response->assertDontSee($originalUrl, false);
});

it('serves a responsive srcset for a gallery strip embedded on a district/campus page', function () {
    Storage::fake('public');

    $district = \App\Models\District::factory()->create();
    $gallery = Gallery::factory()->create(['related_type' => \App\Models\District::class, 'related_id' => $district->id]);
    $gallery->addMedia(UploadedFile::fake()->image('photo.jpg', 2000, 1200))->toMediaCollection('photos');

    $response = $this->get('/en/district-roadshows/'.$district->slug)->assertOk();

    $response->assertSee('srcset=', false);
});
