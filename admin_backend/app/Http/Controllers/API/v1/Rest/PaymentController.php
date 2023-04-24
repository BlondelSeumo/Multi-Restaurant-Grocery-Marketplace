<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Repositories\PaymentRepository\PaymentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends RestBaseController
{
    private PaymentRepository $repository;

    /**
     * @param PaymentRepository $repository
     */
    public function __construct(PaymentRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $payments = $this->repository->paymentsList(['active' => 1]);

        return PaymentResource::collection($payments);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        /** @var Payment $payment */
        $payment = $this->repository->paymentDetails($id);

        if (!$payment || !$payment->active) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            PaymentResource::make($payment)
        );
    }

}
