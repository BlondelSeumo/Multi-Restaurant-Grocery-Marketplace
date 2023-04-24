<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\OrderRefund\UpdateRequest;
use App\Http\Resources\OrderRefundResource;
use App\Models\OrderRefund;
use App\Repositories\OrderRepository\OrderRefundRepository;
use App\Services\OrderService\OrderRefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderRefundsController extends SellerBaseController
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
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $orderRefunds = $this->repository->list($request->merge(['shop_id' => $this->shop->id])->all());

        return OrderRefundResource::collection($orderRefunds);
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $orderRefunds = $this->repository->paginate($request->merge(['shop_id' => $this->shop->id])->all());

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

        if (data_get($orderRefund->order, 'shop_id') !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

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
        if (data_get($orderRefund->order, 'shop_id') !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

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
        $result = $this->service->delete($request->input('ids', []), $this->shop->id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
