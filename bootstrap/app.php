<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'locale' => \App\Http\Middleware\SetLocale::class,
        ]);

        // SSLCommerz posts its IPN and success/fail/cancel callbacks
        // server-to-server (and via a browser auto-submit form), with no
        // Laravel session/CSRF token of ours to present.
        $middleware->validateCsrfTokens(except: [
            'payments/sslcommerz/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
