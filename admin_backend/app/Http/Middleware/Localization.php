<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseError;
use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $default = config('app.locale');

        $lang = $_GET['lang'] ?? null;

        if ($lang) {

            $locales = config('translatable.locales');

            if (!in_array($lang, $locales)) {
                abort(404,  __('errors.' . ResponseError::LANGUAGE_NOT_FOUND, locale: $lang));
            }

            session()->put('session-lang', $lang);
            app()->setLocale($lang);
        }

        // 2. retrieve selected locale if exist (otherwise return the default)
        $locale = session('session-lang', $default);
        // 3. set the locale
        app()->setLocale($locale);

        return $next($request);
    }
}
