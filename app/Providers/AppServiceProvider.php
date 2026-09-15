<?php

namespace App\Providers;

use App\Models\HomepageSection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

require_once __DIR__.'/../Support/helpers.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('seoMeta', function () {
            /** @var Blueprint $this */
            $this->string('seo_title')->nullable();
            $this->string('seo_description', 500)->nullable();
            $this->string('og_image')->nullable();
        });

        View::composer('home', function ($view) {
            $view->with('sections', HomepageSection::visible()->get());
        });
    }
}
