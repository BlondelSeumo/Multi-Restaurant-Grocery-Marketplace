<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends RestBaseController
{
    public function index(): JsonResponse
    {
        $currencies = Currency::currenciesList();

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            CurrencyResource::collection($currencies)
        );
    }

    /**
     * Get all Active languages
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $currencies = Currency::currenciesList()->where('active', 1);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CurrencyResource::collection($currencies)
        );
    }
}
