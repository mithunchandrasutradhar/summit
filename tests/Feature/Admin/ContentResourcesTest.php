<?php

use App\Models\Download;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomepageSection;
use App\Models\MediaCoverage;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Partner;
use App\Models\SuccessStory;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

dataset('content_resources', [
    'pages' => [fn () => Page::factory()->create(), '/admin/pages'],
    'news-posts' => [fn () => NewsPost::factory()->create(), '/admin/news-posts'],
    'success-stories' => [fn () => SuccessStory::factory()->create(), '/admin/success-stories'],
    'media-coverages' => [fn () => MediaCoverage::factory()->create(), '/admin/media-coverages'],
    'partners' => [fn () => Partner::factory()->create(), '/admin/partners'],
    'downloads' => [fn () => Download::factory()->create(), '/admin/downloads'],
    'galleries' => [fn () => Gallery::factory()->create(), '/admin/galleries'],
    'faqs' => [fn () => Faq::factory()->create(), '/admin/faqs'],
]);

it('renders the list, create and edit pages for each content resource', function (Closure $makeRecord, string $basePath) {
    $record = $makeRecord();

    $this->get($basePath)->assertOk();
    $this->get("{$basePath}/create")->assertOk();
    $this->get("{$basePath}/{$record->id}/edit")->assertOk();
})->with('content_resources');

it('renders the homepage sections list, create and edit pages', function () {
    $section = HomepageSection::create([
        'section_key' => 'hero',
        'is_visible' => true,
        'order' => 0,
        'content' => [],
    ]);

    $this->get('/admin/homepage-sections')->assertOk();
    $this->get('/admin/homepage-sections/create')->assertOk();
    $this->get("/admin/homepage-sections/{$section->id}/edit")->assertOk();
});
