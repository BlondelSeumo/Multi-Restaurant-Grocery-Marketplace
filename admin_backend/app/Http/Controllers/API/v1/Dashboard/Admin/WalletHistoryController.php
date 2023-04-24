<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\WalletHistory\ChangeStatusRequest;
use App\Http\Resources\WalletHistoryResource;
use App\Repositories\WalletRepository\WalletHistoryRepository;
use App\Services\WalletHistoryService\WalletHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WalletHistoryController extends AdminBaseController
{
    private WalletHistoryService $service;
    private WalletHistoryRepository $repository;

    public function __construct(WalletHistoryService $service, WalletHistoryRepository $repository)
    {
        parent::__construct();
        $this->service = $service;
        $this->repository = $repository;
    }

    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $walletHistory = $this->repository->walletHistoryPaginate($request->all());

        return WalletHistoryResource::collection($walletHistory);
    }

    public function changeStatus(string $uuid, ChangeStatusRequest $request): JsonResponse
    {

        $result = $this->service->changeStatus($uuid, $request->input('status'));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
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
