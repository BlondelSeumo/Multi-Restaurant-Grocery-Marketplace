<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Resources\ShopSubscriptionResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\ShopSubscription;
use App\Models\Subscription;
use App\Services\SubscriptionService\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends SellerBaseController
{
    private SubscriptionService $service;

    /**
     * @param SubscriptionService $service
     */
    public function __construct(SubscriptionService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::subscriptionsList()->where('active', 1);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SubscriptionResource::collection($subscriptions)
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function mySubscription(Request $request): JsonResponse
    {
        $subscriptions = ShopSubscription::actualSubscription()->with([
            'subscription',
            'transaction'
        ])
            ->where('shop_id', $this->shop->id)
            ->paginate($request->input('perPage', 10));

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ShopSubscriptionResource::collection($subscriptions)
        );
    }

    public function subscriptionAttach(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (empty($subscription)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $this->service->subscriptionAttach($subscription, $this->shop->id)
        );
    }

}
