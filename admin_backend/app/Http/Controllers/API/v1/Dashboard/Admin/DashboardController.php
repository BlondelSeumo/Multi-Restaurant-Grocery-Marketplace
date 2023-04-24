<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Models\User;
use App\Repositories\DashboardRepository\DashboardRepository;
use Artisan;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends AdminBaseController
{
    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function ordersStatistics(FilterParamsRequest $request): JsonResponse
    {
        if (!in_array($request->input('time'), User::DATES)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_508]);
        }

        $result = (new DashboardRepository)->ordersStatistics($request->all());

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ordersChart(Request $request): JsonResponse
    {
        if (!in_array($request->input('time'), User::DATES)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_508]);
        }

        $result = (new DashboardRepository)->ordersChart($request->all());

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function productsStatistic(Request $request): JsonResponse
    {
        if (!in_array($request->input('time'), User::DATES)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_508]);
        }

        $result = (new DashboardRepository)->productsStatistic($request->all());

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function usersStatistic(Request $request): JsonResponse
    {
        if (!in_array($request->input('time'), User::DATES)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_508]);
        }

        $result = (new DashboardRepository)->usersStatistic($request->all());

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }

    /**
     * @return JsonResponse
     */
    public function timeZones(): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            DateTimeZone::listIdentifiers()
        );
    }

    /**
     * @return JsonResponse
     */
    public function timeZone(): JsonResponse
    {
        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), [
            'timeZone'  => config('app.timezone'),
            'time'      => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function timeZoneChange(Request $request): JsonResponse
    {
        $timeZone = $request->input('timezone');
        $oldZone  = config('app.timezone');

        if (empty($timeZone) || !in_array($timeZone, DateTimeZone::listIdentifiers())) {
            $timeZone = $oldZone;
        }

        $path = base_path('config/app.php');

        file_put_contents(
            $path, str_replace(
                "'timezone' => '$oldZone',",
                "'timezone' => '$timeZone',",
                file_get_contents($path)
            )
        );

        Artisan::call('cache:clear');

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $timeZone);
    }
}
