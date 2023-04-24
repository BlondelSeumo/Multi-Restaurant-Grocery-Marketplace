<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\Currency\StoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Services\Interfaces\CurrencyServiceInterface;
use Illuminate\Http\JsonResponse;

class CurrencyController extends AdminBaseController
{
    private CurrencyServiceInterface $service;

    /**
     * @param CurrencyServiceInterface $service
     */
    public function __construct(CurrencyServiceInterface $service)
    {
        parent::__construct();
        $this->service  = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::currenciesList();

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CurrencyResource::collection($currencies)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            CurrencyResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Currency $currency
     * @return JsonResponse
     */
    public function show(Currency $currency): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CurrencyResource::make($currency)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Currency $currency
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(Currency $currency, StoreRequest $request): JsonResponse
    {
        $result = $this->service->update($currency, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            CurrencyResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $this->service->delete($request->input('ids', []));

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
    /**
     * @return JsonResponse
     */
    public function dropAll(): JsonResponse
    {
        $this->service->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->service->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->service->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * Get Currency where "default = 1".
     *
     * @return JsonResponse
     */
    public function getDefaultCurrency(): JsonResponse
    {
        $currency = Currency::whereDefault(1)->first();

        if (empty($currency)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language), CurrencyResource::make($currency)
        );
    }

    /**
     * Get all Active languages
     * @return JsonResponse
     */
    public function getActiveCurrencies(): JsonResponse
    {
        $languages = Currency::whereActive(1)->get();

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CurrencyResource::collection($languages)
        );
    }

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function setActive(int $id): JsonResponse
    {
        $currency = Currency::find($id);

        if (empty($currency)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $currency->update(['active' => !$currency->active]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            CurrencyResource::make($currency)
        );
    }
}
