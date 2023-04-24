<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\DeliveryZone\AdminRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\DeliveryZoneResource;
use App\Models\DeliveryZone;
use App\Repositories\DeliveryZoneRepository\DeliveryZoneRepository;
use App\Services\DeliveryZoneService\DeliveryZoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryZoneController extends AdminBaseController
{
    private DeliveryZoneService $service;
    private DeliveryZoneRepository $repository;

    /**
     * @param DeliveryZoneService $service
     * @param DeliveryZoneRepository $repository
     */
    public function __construct(DeliveryZoneService $service, DeliveryZoneRepository $repository)
    {
        parent::__construct();

        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $deliveryZone = $this->repository->paginate($request->all());

        return DeliveryZoneResource::collection($deliveryZone);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $data = $request->validated();

        $exist = DeliveryZone::where('shop_id', data_get($data, 'shop_id'))->first();

        if (!empty($exist)) {
            return $this->update($exist, $request);
        }

        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param DeliveryZone $deliveryZone
     * @return JsonResponse
     */
    public function show(DeliveryZone $deliveryZone): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            DeliveryZoneResource::make($this->repository->show($deliveryZone))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DeliveryZone $deliveryZone
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function update(DeliveryZone $deliveryZone, AdminRequest $request): JsonResponse
    {
        $result = $this->service->update($deliveryZone, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(__('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->service->delete($request->input('ids', []));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

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
