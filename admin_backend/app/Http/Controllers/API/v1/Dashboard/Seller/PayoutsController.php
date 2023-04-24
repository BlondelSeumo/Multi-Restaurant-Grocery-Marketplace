<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Payout\StoreRequest;
use App\Http\Requests\Payout\UpdateRequest;
use App\Http\Resources\PayoutResource;
use App\Models\Payout;
use App\Repositories\PayoutsRepository\PayoutsRepository;
use App\Services\PayoutService\PayoutService;
use Illuminate\Http\JsonResponse;

class PayoutsController extends SellerBaseController
{
    private PayoutsRepository $repository;
    private PayoutService $service;

    /**
     * @param PayoutsRepository $repository
     * @param PayoutService $service
     */
    public function __construct(PayoutsRepository $repository, PayoutService $service)
    {
        parent::__construct();
        $this->repository   = $repository;
        $this->service      = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function index(FilterParamsRequest $request): JsonResponse
    {
        $payouts = $this->repository->paginate($request->merge(['created_by' => auth('sanctum')->id()])->all());

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            PayoutResource::collection($payouts)
        );
    }

    /**
     * NOT USED
     * Display the specified resource.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = auth('sanctum')->id();

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(ResponseError::NO_ERROR, []);
    }

    /**
     * Display the specified resource.
     *
     * @param Payout $payout
     * @return JsonResponse
     */
    public function show(Payout $payout): JsonResponse
    {
        if ($payout->created_by !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        $payout = $this->repository->show($payout);

        return $this->successResponse(ResponseError::NO_ERROR, PayoutResource::make($payout));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Payout $payout
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Payout $payout, UpdateRequest $request): JsonResponse
    {
        if ($payout->created_by !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        $result = $this->service->update($payout, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language)
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
        $this->service->delete($request->input('ids', []));

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
