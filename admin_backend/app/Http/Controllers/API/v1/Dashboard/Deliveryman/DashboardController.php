<?php

namespace App\Http\Controllers\API\v1\Dashboard\Deliveryman;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Repositories\DashboardRepository\DashboardRepository;
use Illuminate\Http\JsonResponse;

class DashboardController extends DeliverymanBaseController
{
    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function countStatistics(FilterParamsRequest $request): JsonResponse
    {
        $filter = $request->merge(['deliveryman' => auth('sanctum')->id()])->all();

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            (new DashboardRepository)->orderByStatusStatistics($filter)
        );
    }
}
