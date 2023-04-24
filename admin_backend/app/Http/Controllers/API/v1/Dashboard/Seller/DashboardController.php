<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Models\User;
use App\Repositories\DashboardRepository\DashboardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends SellerBaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ordersStatistics(Request $request): JsonResponse
    {
        if (!in_array($request->input('time'), User::DATES)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_508]);
        }

        $result = (new DashboardRepository)->ordersStatistics(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

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

        $result = (new DashboardRepository)->ordersChart(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

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

        $result = (new DashboardRepository)->productsStatistic(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

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

        $result = (new DashboardRepository)->usersStatistic(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }
}
