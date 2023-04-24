<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\OrderRefund\UpdateRequest;
use App\Http\Resources\OrderRefundResource;
use App\Models\OrderRefund;
use App\Repositories\OrderRepository\OrderRefundRepository;
use App\Services\OrderService\OrderRefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderRefundsController extends AdminBaseController
{
    private OrderRefundRepository $repository;
    private OrderRefundService $service;

    /**
     * @param OrderRefundRepository $repository
     * @param OrderRefundService $service
     */
    public function __construct(OrderRefundRepository $repository, OrderRefundService $service)
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
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $orderRefunds = $this->repository->list($request->all());

        return OrderRefundResource::collection($orderRefunds);
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $orderRefunds = $this->repository->paginate($request->all());

        return OrderRefundResource::collection($orderRefunds);
    }

    /**
     * Display the specified resource.
     *
     * @param OrderRefund $orderRefund
     * @return JsonResponse
     */
    public function show(OrderRefund $orderRefund): JsonResponse
    {
        $orderRefund = $this->repository->show($orderRefund);

        return $this->successResponse(ResponseError::NO_ERROR, OrderRefundResource::make($orderRefund));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRefund $orderRefund
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(OrderRefund $orderRefund, UpdateRequest $request): JsonResponse
    {
        $result = $this->service->update($orderRefund, $request->validated());

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
        $result = $this->service->delete($request->input('ids', []), null, true);

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
