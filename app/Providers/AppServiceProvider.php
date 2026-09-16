<?php

namespace App\Providers;

use App\Models\HomepageSection;
use App\Policies\RolePolicy;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\SslcommerzGateway;
use App\Services\Sms\LogSmsDriver;
use App\Services\Sms\SmsGateway;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

require_once __DIR__.'/../Support/helpers.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap for a real BD SMS provider (e.g. SSL Wireless, Alpha SMS)
        // once the organizing team selects one.
        $this->app->bind(SmsGateway::class, LogSmsDriver::class);

        $this->app->bind(PaymentGateway::class, SslcommerzGateway::class);
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

        // This app has no conventional Laravel login route — only
        // Filament's admin panel login. Point auth-protected routes
        // outside the panel (e.g. the gate check-in scanner) there
        // instead of erroring on route('login').
        Authenticate::redirectUsing(fn () => route('filament.admin.auth.login'));

        // Spatie's Role model lives outside App\Models, so Laravel's policy
        // auto-discovery never finds RolePolicy on its own — without this,
        // Filament Shield's Roles resource falls back to "no policy = allowed"
        // and any panel user (including Content Editor/Awards Jury) could grant
        // themselves Super Admin. Shield's own permission matrix was never
        // generated/seeded, so RolePolicy checks the role directly rather than
        // via ->can() on ungenerated permissions.
        Gate::policy(Role::class, RolePolicy::class);
    }
}
