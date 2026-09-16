<?php

use App\Models\Event;
use App\Settings\GeneralSettings;

it('renders canonical, hreflang x-default, and Open Graph tags on the homepage', function () {
    $response = $this->get('/en/')->assertOk();

    $response->assertSee('rel="canonical"', false);
    $response->assertSee('hreflang="x-default"', false);
    $response->assertSee('property="og:title"', false);
    $response->assertSee('property="og:description"', false);
    $response->assertSee('name="twitter:card"', false);
});

it('renders the GA snippet when a measurement id is configured, and omits it otherwise', function () {
    $this->get('/en/')->assertOk()->assertDontSee('googletagmanager.com/gtag/js', false);

    $settings = app(GeneralSettings::class);
    $settings->ga_measurement_id = 'G-TESTID123';
    $settings->save();

    $this->get('/en/')->assertOk()->assertSee('googletagmanager.com/gtag/js?id=G-TESTID123', false);
});

it('serves a sitemap with the homepage and hreflang-equivalent locale entries', function () {
    $response = $this->get('/sitemap.xml')->assertOk();

    $response->assertHeader('content-type', 'text/xml; charset=utf-8');
    $response->assertSee(url('/en/'), false);
    $response->assertSee(url('/bn/'), false);
});

it('renders Event structured data on the Grand Summit page when a current event exists', function () {
    Event::factory()->create(['is_current' => true, 'name' => 'Freelancer Summit Bangladesh 2026']);

    $response = $this->get('/en/grand-summit')->assertOk();

    $response->assertSee('application/ld+json', false);
    $response->assertSee('"@type":"Event"', false);
});
