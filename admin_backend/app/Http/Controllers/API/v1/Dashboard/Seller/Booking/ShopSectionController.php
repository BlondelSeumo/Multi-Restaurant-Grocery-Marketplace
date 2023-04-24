<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller\Booking;

use App\Helpers\ResponseError;
use App\Http\Controllers\API\v1\Dashboard\Seller\SellerBaseController;
use App\Http\Requests\Booking\ShopSection\SellerStoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Booking\ShopSectionResource;
use App\Models\Booking\ShopSection;
use App\Repositories\Booking\ShopSectionRepository\ShopSectionRepository;
use App\Services\Booking\ShopSectionService\ShopSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopSectionController extends SellerBaseController
{
    /**
     * @param ShopSectionService $service
     * @param ShopSectionRepository $repository
     */
    public function __construct(private ShopSectionService $service, private ShopSectionRepository $repository)
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

        return ShopSectionResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SellerStoreRequest $request
     * @return JsonResponse
     */
    public function store(SellerStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ShopSectionResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param ShopSection $shopSection
     * @return JsonResponse
     */
    public function show(ShopSection $shopSection): JsonResponse
    {
        $result = $this->repository->show($shopSection);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ShopSectionResource::make($result)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShopSection $shopSection
     * @param SellerStoreRequest $request
     * @return JsonResponse
     */
    public function update(ShopSection $shopSection, SellerStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->update($shopSection, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ShopSectionResource::make(data_get($result, 'data'))
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
