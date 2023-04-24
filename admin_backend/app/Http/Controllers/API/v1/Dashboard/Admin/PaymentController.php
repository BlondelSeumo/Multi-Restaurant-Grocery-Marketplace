<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Payment\UpdateRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Repositories\PaymentRepository\PaymentRepository;
use App\Services\PaymentService\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends AdminBaseController
{
    private PaymentRepository $paymentRepository;
    private PaymentService $service;

    public function __construct(PaymentRepository $paymentRepository, PaymentService $service)
    {
        parent::__construct();
        $this->paymentRepository = $paymentRepository;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $payments = $this->paymentRepository->paymentsList($request->all());

        return PaymentResource::collection($payments);
    }


    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return JsonResponse
     */
    public function show(Payment $payment): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            PaymentResource::make($payment->load([
                'translations',
                'translation' => $this->language
            ]))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Payment $payment
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Payment $payment, UpdateRequest $request): JsonResponse
    {
        $result = $this->service->update($payment, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            PaymentResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Set Model Active.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function setActive(int $id): JsonResponse
    {
        $result = $this->service->setActive($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            PaymentResource::make(data_get($result, 'data'))
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
