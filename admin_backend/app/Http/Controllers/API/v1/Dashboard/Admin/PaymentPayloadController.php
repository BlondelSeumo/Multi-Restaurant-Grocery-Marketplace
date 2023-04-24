<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\PaymentPayload\UpdateRequest;
use App\Http\Requests\PaymentPayload\StoreRequest;
use App\Http\Resources\PaymentPayloadResource;
use App\Repositories\PaymentPayloadRepository\PaymentPayloadRepository;
use App\Services\PaymentPayloadService\PaymentPayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentPayloadController extends AdminBaseController
{

    public function __construct(private PaymentPayloadService $service, private PaymentPayloadRepository $repository)
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
        $model = $this->repository->paginate($request->all());

        return PaymentPayloadResource::collection($model);
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

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return data_get($result, 'params') ?
                $this->requestErrorResponse(
                    data_get($result, 'status'),
                    data_get($result, 'code'),
                    data_get($result, 'params'),
                    data_get($result, 'http', 422),
                )
                : $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            PaymentPayloadResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param int $paymentId
     * @return JsonResponse
     */
    public function show(int $paymentId): JsonResponse
    {
        $model = $this->repository->show($paymentId);

        if (empty($model)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            PaymentPayloadResource::make($model)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $paymentId
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(int $paymentId, UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->service->update($paymentId, $validated);

        if (!data_get($result, 'status')) {
            return data_get($result, 'params') ?
                $this->requestErrorResponse(
                    data_get($result, 'status'),
                    data_get($result, 'code'),
                    data_get($result, 'params'),
                    data_get($result, 'http', 422),
                )
                : $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            PaymentPayloadResource::make(data_get($result, 'data'))
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
        $result = $this->service->delete($request->input('ids', []));

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
