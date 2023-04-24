<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Order\AddReviewRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Settings;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepoInterface;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\OrderService\OrderReviewService;
use App\Services\OrderService\OrderStatusUpdateService;
use App\Traits\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends UserBaseController
{
    use Notification;

    private OrderRepoInterface $orderRepository;
    private OrderServiceInterface $orderService;

    /**
     * @param OrderRepoInterface $orderRepository
     * @param OrderServiceInterface $orderService
     */
    public function __construct(OrderRepoInterface $orderRepository, OrderServiceInterface $orderService)
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $filter = $request->merge(['user_id' => auth('sanctum')->id()])->all();

        $orders = $this->orderRepository->ordersPaginate($filter, 'paginate');

        return OrderResource::collection($orders);
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

        if ((int)data_get(Settings::adminSettings()->where('key', 'order_auto_approved')->first(), 'value') === 1) {
            $validated['status'] = Order::STATUS_ACCEPTED;
        }

        $validated['user_id'] = auth('sanctum')->id();

        /** @var Cart $cart */
        $cart = Cart::with([
            'userCarts:id,cart_id',
            'userCarts.cartDetails:id'
        ])
            ->select('id')
            ->find(data_get($validated, 'cart_id'));

        if (empty($cart)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        } else if ($cart->user_carts_count === 0) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::USER_CARTS_IS_EMPTY, locale: $this->language)
            ]);
        } else if ($cart->userCarts()->withCount('cartDetails')->get()->sum('cart_details_count') === 0) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::PRODUCTS_IS_EMPTY, locale: $this->language)
            ]);
        }

        $result = $this->orderService->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        $this->sendNotification(
            $this->tokens($result),
            "New order was created",
            data_get($result, 'data.id'),
            data_get($result, 'data')?->setAttribute('type', 'new_order')?->only(['id', 'status'])
        );

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            $this->orderRepository->reDataOrder(data_get($result, 'data'))
        );
    }

    public function tokens($result): array
    {
        $adminFirebaseTokens = User::with([
            'roles' => fn($q) => $q->where('name', 'admin')
        ])
            ->whereHas('roles', fn($q) => $q->where('name', 'admin') )
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->toArray();

        $sellersFirebaseTokens = User::with([
            'shop' => fn($q) => $q->where('id', data_get($result, 'data.shop_id'))
        ])
            ->whereHas('shop', fn($q) => $q->where('id', data_get($result, 'data.shop_id')))
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->toArray();

        $aTokens = [];
        $sTokens = [];

        foreach ($adminFirebaseTokens as $adminToken) {
            $aTokens = array_merge($aTokens, is_array($adminToken) ? array_values($adminToken) : [$adminToken]);
        }

        foreach ($sellersFirebaseTokens as $sellerToken) {
            $sTokens = array_merge($aTokens, is_array($sellerToken) ? array_values($sellerToken) : [$sellerToken]);
        }

        return array_values(array_unique(array_merge($aTokens, $sTokens)));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->orderById($id);

        if (optional($order)->user_id !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        return $this->successResponse(ResponseError::NO_ERROR, $this->orderRepository->reDataOrder($order));
    }

    /**
     * Add Review to OrderDetails.
     *
     * @param int $id
     * @param AddReviewRequest $request
     * @return JsonResponse
     */
    public function addOrderReview(int $id, AddReviewRequest $request): JsonResponse
    {
        /** @var Order $order */
        $order = Order::with(['review', 'reviews'])->find($id);

        if (optional($order)->user_id !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' =>  __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        $result = (new OrderReviewService)->addReview($order, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            ResponseError::NO_ERROR,
            $this->orderRepository->reDataOrder(data_get($result, 'data'))
        );
    }

    /**
     * Add Review to OrderDetails.
     *
     * @param int $id
     * @param AddReviewRequest $request
     * @return JsonResponse
     */
    public function addDeliverymanReview(int $id, AddReviewRequest $request): JsonResponse
    {
        $result = (new OrderReviewService)->addDeliverymanReview($id, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            ResponseError::NO_ERROR,
            $this->orderRepository->reDataOrder(data_get($result, 'data'))
        );
    }

    /**
     * Add Review to OrderDetails.
     *
     * @param int $id
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function orderStatusChange(int $id, FilterParamsRequest $request): JsonResponse
    {
        if (!$request->input('status')) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_254,
                'message' => __('errors.' . ResponseError::EMPTY_STATUS, locale: $this->language)
            ]);
        }

        /** @var Order  $order */
        $order = Order::with([
            'shop.seller',
            'deliveryMan',
            'user.wallet',
        ])->find($id);

        if (!$order) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = (new OrderStatusUpdateService)->statusUpdate($order, $request->input('status'));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            ResponseError::NO_ERROR,
            $this->orderRepository->reDataOrder(data_get($result, 'data'))
        );
    }
}
