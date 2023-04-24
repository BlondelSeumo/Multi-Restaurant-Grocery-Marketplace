<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseError;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next, $role): mixed
    {
        $roles = is_array($role) ? $role : explode('|', $role);

        if (auth('sanctum')->user()?->hasAnyRole($roles) || auth('sanctum')->user()?->hasRole('admin')) {
            return $next($request);
        }

        return $this->onErrorResponse([
            'code'      => ResponseError::ERROR_101,
            'message'   => __('errors.' . ResponseError::ERROR_101, locale: request('language', 'en')),
            'http'      => Response::HTTP_FORBIDDEN
        ]);
    }
}
