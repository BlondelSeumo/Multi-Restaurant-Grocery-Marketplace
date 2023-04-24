<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\CouponCheckRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CouponController extends RestBaseController
{
    use ApiResponse;
    private Coupon $model;

    public function __construct(Coupon $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Handle the incoming request.
     *
     * @param CouponCheckRequest $request
     * @return JsonResponse
     */
    public function check(CouponCheckRequest $request): JsonResponse
    {
        $coupon = Coupon::checkCoupon($request->input('coupon'))->first();

        if (empty($coupon)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_250,
                'message' => __('errors.' . ResponseError::ERROR_250, locale: $this->language)
            ]);
        }

        $result = OrderCoupon::where(function ($q) use ($request) {
                $q->where('user_id', $request->input('user_id'))
                    ->orWhere('user_id', auth('sanctum')->id());
            })
            ->where('name', $request->input('coupon'))
            ->first();

        if (empty($result)) {
            return $this->successResponse(
                __('errors.' . ResponseError::SUCCESS, locale: $this->language),
                CouponResource::make($coupon)
            );
        }

        return $this->onErrorResponse([
            'code'    => ResponseError::ERROR_251,
            'message' => __('errors.' . ResponseError::ERROR_251, locale: $this->language)
        ]);
    }
}
