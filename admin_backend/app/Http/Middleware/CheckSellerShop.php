<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseError;
use App\Models\User;
use App\Traits\ApiResponse;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class CheckSellerShop
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        if (!auth('sanctum')->check()) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_100,
                'message'   => __('errors.' . ResponseError::ERROR_100, locale: request('language', 'en'))
            ]);
        }

        /** @var User $user */
        $user = auth('sanctum')->user();

        if ($user?->shop && $user?->role == 'seller') {
            return $next($request);
        }

        if ($user?->moderatorShop && $user?->role == 'moderator' || $user?->role == 'deliveryman') {
            return $next($request);
        }

        return $this->onErrorResponse([
            'code'      => ResponseError::ERROR_204,
            'message'   => __('errors.' . ResponseError::ERROR_204, locale: request('language', 'en'))
        ]);
    }
}
