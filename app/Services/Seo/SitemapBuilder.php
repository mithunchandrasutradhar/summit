<?php

namespace App\Services\Seo;

use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Speaker;
use App\Models\SuccessStory;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Built on demand (content scale here is a few hundred records, so there's
 * no need for a cached/scheduled generation step) and covers every locale,
 * since each is a distinct indexable URL.
 */
class SitemapBuilder
{
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        foreach (config('app.supported_locales') as $locale) {
            $sitemap->add(Url::create(route('home', ['locale' => $locale]))->setPriority(1.0));

            foreach ([
                'national-journey.index',
                'divisions.index',
                'districts.index',
                'campus-programs.index',
                'grand-summit.index',
                'agenda.index',
                'speakers.index',
                'awards.index',
                'awards.categories',
                'awards.shortlisted',
                'awards.winners',
                'sponsors.index',
                'sponsorship.opportunity',
                'exhibition.index',
                'forum.index',
                'news.index',
                'success-stories.index',
                'media.index',
                'partners.index',
            ] as $routeName) {
                $sitemap->add(Url::create(route($routeName, ['locale' => $locale]))->setPriority(0.8));
            }

            foreach (Division::all() as $division) {
                $sitemap->add(Url::create(route('divisions.show', ['locale' => $locale, 'slug' => $division->slug])));
            }

            foreach (District::forCurrentEvent()->get() as $district) {
                $sitemap->add(Url::create(route('districts.show', ['locale' => $locale, 'slug' => $district->slug])));
            }

            foreach (CampusProgram::forCurrentEvent()->get() as $campusProgram) {
                $sitemap->add(Url::create(route('campus-programs.show', ['locale' => $locale, 'slug' => $campusProgram->slug])));
            }

            foreach (Speaker::published()->get() as $speaker) {
                $sitemap->add(Url::create(route('speakers.show', ['locale' => $locale, 'slug' => $speaker->slug])));
            }

            foreach (NewsPost::published()->get() as $post) {
                $sitemap->add(Url::create(route('news.show', ['locale' => $locale, 'slug' => $post->slug])));
            }

            foreach (SuccessStory::published()->get() as $story) {
                $sitemap->add(Url::create(route('success-stories.show', ['locale' => $locale, 'slug' => $story->slug])));
            }

            foreach (Page::where('is_published', true)->get() as $page) {
                $sitemap->add(Url::create(route('pages.show', ['locale' => $locale, 'slug' => $page->slug])));
            }
        }

        return $sitemap;
    }
}
