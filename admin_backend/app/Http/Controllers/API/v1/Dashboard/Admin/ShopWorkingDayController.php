<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\ShopWorkingDay\AdminRequest;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopWorkingDayResource;
use App\Models\Shop;
use App\Repositories\ShopWorkingDayRepository\ShopWorkingDayRepository;
use App\Services\ShopWorkingDayService\ShopWorkingDayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopWorkingDayController extends AdminBaseController
{
    private ShopWorkingDayRepository $repository;
    private ShopWorkingDayService $service;

    /**
     * @param ShopWorkingDayRepository $repository
     * @param ShopWorkingDayService $service
     */
    public function __construct(ShopWorkingDayRepository $repository, ShopWorkingDayService $service)
    {
        parent::__construct();
        $this->repository   = $repository;
        $this->service      = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $model = $this->repository->paginate($request->all());

        return ShopResource::collection($model);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $shop = Shop::whereUuid($uuid)->first();

        if (empty($shop)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $shopWorkingDays = $this->repository->show($shop->id);

        return $this->successResponse(ResponseError::NO_ERROR, [
            'dates' => ShopWorkingDayResource::collection($shopWorkingDays),
            'shop'  => ShopResource::make($shop),
        ]);
    }

    /**
     * NOT USED
     * Display the specified resource.
     *
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(ResponseError::NO_ERROR, []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $uuid
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, AdminRequest $request): JsonResponse
    {
        $shop = Shop::whereUuid($uuid)->first();

        if (empty($shop)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->service->update($shop->id, $request->validated());

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

}
