<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends UserBaseController
{
    private TransactionRepository $transactionRepository;

    /**
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        parent::__construct();
        $this->transactionRepository = $transactionRepository;
    }

    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $transactions = $this->transactionRepository->paginate(
            $request->merge(['user_id' => auth('sanctum')->id()])->all()
        );

        return TransactionResource::collection($transactions);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionRepository->show($id);

        if ($transaction?->user_id !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(ResponseError::NO_ERROR, TransactionResource::make($transaction));
    }
}
