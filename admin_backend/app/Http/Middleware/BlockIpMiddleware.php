<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlockIpMiddleware
{
    protected array $allowRoutes = [
        'api/v1/install/*',
        'api/v1/rest/*',
        'api/v1/dashboard/galleries/*',
        'api/v1/auth/*',
        'api/v1/webhook/*',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!$request->is($this->allowRoutes) && in_array($request->ip(), collect(Cache::get('block-ips'))->toArray())) {
            abort(403);
        }
        return $next($request);
    }
}
