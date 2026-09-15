<?php

use Illuminate\Support\Facades\App;

if (! function_exists('lroute')) {
    /**
     * Generate a URL for a locale-prefixed named route, automatically
     * filling in the current app locale so views don't have to repeat
     * ['locale' => app()->getLocale()] on every route() call.
     */
    function lroute(string $name, array $parameters = [], bool $absolute = true): string
    {
        return route($name, array_merge(['locale' => App::getLocale()], $parameters), $absolute);
    }
}
