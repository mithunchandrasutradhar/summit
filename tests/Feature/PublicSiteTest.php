<?php

use App\Models\Event;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\SuccessStory;

beforeEach(function () {
    Event::factory()->create(['is_current' => true]);
});

it('redirects the root url to the default locale', function () {
    $this->get('/')->assertRedirect('/en/');
});

it('serves the homepage in english', function () {
    $this->get('/en/')->assertOk()->assertSee('Freelancer Summit Bangladesh 2026');
});

it('serves the homepage in bangla', function () {
    $this->get('/bn/')->assertOk();
});

it('404s on an unsupported locale', function () {
    $this->get('/xx/')->assertNotFound();
});

// Regression: a controller action nested under the {locale} prefix group
// must declare ALL of the route's segments (even unused ones) — Laravel
// falls back to positional binding for an unmatched primitive parameter,
// which previously handed these actions the locale value instead of the
// intended slug and made every one of them 404.
it('resolves the slug parameter correctly on the pages.show route', function () {
    $page = Page::factory()->create(['slug' => 'about-us']);
    $this->get("/en/{$page->slug}")->assertOk()->assertSee($page->title);
});

it('resolves the slug parameter correctly on the news.show route', function () {
    $post = NewsPost::factory()->create();
    $this->get("/en/news/{$post->slug}")->assertOk()->assertSee($post->title);
});

it('resolves the slug parameter correctly on the success-stories.show route', function () {
    $story = SuccessStory::factory()->create();
    $this->get("/en/success-stories/{$story->slug}")->assertOk()->assertSee($story->freelancer_name);
});
