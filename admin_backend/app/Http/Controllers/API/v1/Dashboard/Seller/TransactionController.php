<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends SellerBaseController
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
        $transactions = $this->transactionRepository->paginate($request->merge(['shop_id' => $this->shop->id])->all());

        return TransactionResource::collection($transactions);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionRepository->show($id, $this->shop->id);

        if (empty($transaction)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(ResponseError::NO_ERROR, TransactionResource::make($transaction));
    }
}
