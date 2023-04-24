<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Order\SellerOrderReportRequest;
use App\Http\Resources\SellerOrderReportResource;
use App\Repositories\OrderRepository\SellerOrderReportRepository;
use App\Traits\Notification;
use Illuminate\Http\JsonResponse;

class OrderReportController extends SellerBaseController
{
    use Notification;

    /**
     * @param SellerOrderReportRepository $repository
     */
    public function __construct(
        private SellerOrderReportRepository $repository
    )
    {
        parent::__construct();
    }

    public function report(SellerOrderReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $this->repository->report($validated)
        );
    }

    public function reportPaginate(FilterParamsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SellerOrderReportResource::collection($this->repository->reportPaginate($validated))
        );
    }
}
