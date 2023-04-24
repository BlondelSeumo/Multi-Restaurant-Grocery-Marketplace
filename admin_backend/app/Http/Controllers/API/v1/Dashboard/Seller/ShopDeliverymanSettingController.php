<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\ShopDeliverymanSetting\StoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\ShopDeliverymanSettingResource;
use App\Models\ShopDeliverymanSetting;
use App\Repositories\ShopRepository\ShopDeliverymanSettingRepository;
use App\Services\ShopServices\ShopDeliverymanSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopDeliverymanSettingController extends SellerBaseController
{
    public function __construct(
        private ShopDeliverymanSettingService $service,
        private ShopDeliverymanSettingRepository $repository
    )
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $model = $this->repository->paginate($request->merge(['shop_id' => $this->shop->id])->all());

        return ShopDeliverymanSettingResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ShopDeliverymanSettingResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param ShopDeliverymanSetting $shopDeliverymanSetting
     * @return JsonResponse
     */
    public function show(ShopDeliverymanSetting $shopDeliverymanSetting): JsonResponse
    {
        if ($shopDeliverymanSetting->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->repository->show($shopDeliverymanSetting);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ShopDeliverymanSettingResource::make($result)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShopDeliverymanSetting $shopDeliverymanSetting
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(ShopDeliverymanSetting $shopDeliverymanSetting, StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        if ($shopDeliverymanSetting->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->service->update($shopDeliverymanSetting, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ShopDeliverymanSettingResource::make(data_get($result, 'data'))
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
        $result = $this->service->delete($request->input('ids', []), $this->shop->id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
