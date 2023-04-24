<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository\TransactionRepository;
use App\Services\TransactionService\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends AdminBaseController
{
    private TransactionRepository $transactionRepository;
    private TransactionService $service;

    /**
     * @param TransactionRepository $transactionRepository
     * @param TransactionService $service
     */
    public function __construct(TransactionRepository $transactionRepository, TransactionService $service)
    {
        parent::__construct();
        $this->transactionRepository = $transactionRepository;
        $this->service = $service;
    }

    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $transactions = $this->transactionRepository->paginate($request->all());

        return TransactionResource::collection($transactions);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionRepository->show($id);

        if (empty($transaction)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(ResponseError::NO_ERROR, TransactionResource::make($transaction));
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
