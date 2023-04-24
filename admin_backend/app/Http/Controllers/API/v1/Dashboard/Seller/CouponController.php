<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\Coupon\StoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Repositories\CouponRepository\CouponRepository;
use App\Services\CouponService\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends SellerBaseController
{
    private CouponRepository $couponRepository;
    private CouponService $couponService;

    /**
     * @param CouponRepository $couponRepository
     * @param CouponService $couponService
     */
    public function __construct(CouponRepository $couponRepository, CouponService $couponService)
    {
        parent::__construct();
        $this->couponRepository = $couponRepository;
        $this->couponService    = $couponService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $coupons = $this->couponRepository->couponsList($request->all());

        return CouponResource::collection($coupons);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $coupons = $this->couponRepository->couponsPaginate($request->merge(['shop_id' => $this->shop->id])->all());

        return CouponResource::collection($coupons);
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
        $validated['shop_id'] = $this->shop->id;

        $result = $this->couponService->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            CouponResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Coupon $coupon
     * @return JsonResponse
     */
    public function show(Coupon $coupon): JsonResponse
    {
        $coupon->load([
            'translation' => fn($q) => $q->where('locale', $this->language)->select('id', 'coupon_id', 'locale', 'title'),
            'translations'
        ]);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CouponResource::make($coupon)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Coupon $coupon
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(Coupon $coupon, StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->couponService->update($coupon, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            CouponResource::make(data_get($result, 'data'))
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
        $coupons = Coupon::whereIn('id', $request->input('ids', []))
            ->where('shop_id', $this->shop->id)
            ->get();

        foreach ($coupons as $coupon) {
            $coupon->delete();
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
