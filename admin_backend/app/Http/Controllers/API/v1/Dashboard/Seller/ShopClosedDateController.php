<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\ShopClosedDate\SellerRequest;
use App\Http\Resources\ShopClosedDateResource;
use App\Http\Resources\ShopResource;
use App\Repositories\ShopClosedDateRepository\ShopClosedDateRepository;
use App\Services\ShopClosedDateService\ShopClosedDateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ShopClosedDateController extends SellerBaseController
{
    private ShopClosedDateRepository $repository;
    private ShopClosedDateService $service;

    /**
     * @param ShopClosedDateRepository $repository
     * @param ShopClosedDateService $service
     */
    public function __construct(ShopClosedDateRepository $repository, ShopClosedDateService $service)
    {
        parent::__construct();
        $this->repository   = $repository;
        $this->service      = $service;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        Artisan::call('remove:expired:closed:dates');

        return $this->show($this->shop->uuid);
    }

    /**
     * NOT USED
     * Display the specified resource.
     *
     * @param SellerRequest $request
     * @return JsonResponse
     */
    public function store(SellerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(ResponseError::NO_ERROR, []);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        if ($this->shop->uuid != $uuid) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_101]);
        }

        $shopClosedDate = $this->repository->show($this->shop->id);

        return $this->successResponse(ResponseError::NO_ERROR, [
            'closed_dates'  => ShopClosedDateResource::collection($shopClosedDate),
            'shop'          => ShopResource::make($this->shop),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $uuid
     * @param SellerRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, SellerRequest $request): JsonResponse
    {
        if ($this->shop->uuid != $uuid) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_101]);
        }

        $result = $this->service->update($this->shop->id, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $this->service->delete($request->input('ids', []), $this->shop->id);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
