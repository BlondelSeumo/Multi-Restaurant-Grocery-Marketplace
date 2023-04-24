<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseError;
use App\Traits\ApiResponse;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckOnlineUser
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check()) {
            $expiredAt = now()->addMinutes(3);
            Cache::put('user-online-' . auth('sanctum')->id(), true, $expiredAt);
        }
        return $next($request);
    }
}
