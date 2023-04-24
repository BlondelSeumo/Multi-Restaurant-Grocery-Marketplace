<?php

namespace App\Services\OrderService;

use App\Helpers\NotificationHelper;
use App\Helpers\ResponseError;
use App\Helpers\Utility;
use App\Models\Receipt;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Settings;
use App\Models\Shop;
use App\Models\User;
use App\Services\CartService\CartService;
use App\Services\CoreService;
use App\Services\Interfaces\OrderServiceInterface;
use App\Traits\Notification;
use DB;
use Exception;
use Throwable;

class OrderService extends CoreService implements OrderServiceInterface
{
    use Notification;

    protected function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $order = DB::transaction(function () use ($data) {

                $shop = Shop::find(data_get($data, 'shop_id', 0));

                /** @var Order $order */
                $order = $this->model()->create($this->setOrderParams($data, $shop));

                if (data_get($data, 'images.0')) {
                    $order->update(['img' => data_get($data, 'images.0')]);
                    $order->uploads(data_get($data, 'images'));
                }

                if (data_get($data, 'cart_id')) {
                    $order = (new OrderDetailService)->createOrderUser($order, data_get($data, 'cart_id', 0));
                } else {
                    $order = (new OrderDetailService)->create($order, data_get($data, 'products', []));
                }

                $this->calculateOrder($order, $shop, $data);

                return $order;
            });

            return [
                'status'    => true,
                'message'   => ResponseError::NO_ERROR,
                'data'      => $order->fresh([
                    'shop',
                    'coupon',
                    'currency',
                    'orderDetails' => fn($q) => $q->whereNull('parent_id'),
                    'orderDetails.stock.countable.discounts' => fn($q) => $q->where('start', '<=', today())
                        ->where('end', '>=', today())
                        ->where('active', 1),
                    'orderDetails.stock.countable:id,status,shop_id,min_qty,max_qty,tax,img',
                    'orderDetails.children.stock.countable:id,status,shop_id,min_qty,max_qty,tax',
                ])
            ];

        } catch (Throwable $e) {
            $this->error($e);

            return [
                'status'    => false,
                'message'   => __('errors.' . ResponseError::ERROR_501, locale: $this->language),
                'code'      => ResponseError::ERROR_501
            ];
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array
    {
        try {
            $order = DB::transaction(function () use ($data, $id) {

                $shop = Shop::find(data_get($data, 'shop_id', 0));

                /** @var Order $order */
                $order = $this->model()->with('orderDetails')->find($id);

                if (!$order) {
                    throw new Exception(__('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language));
                }

                $data['user_id'] = $order->user_id;

                $order->update($this->setOrderParams($data, $shop));

                if (data_get($data, 'images.0')) {

                    $order->galleries()->delete();
                    $order->update(['img' => data_get($data, 'images.0')]);
                    $order->uploads(data_get($data, 'images'));

                }

                $order = (new OrderDetailService)->create($order, data_get($data, 'products', []));

                $this->calculateOrder($order, $shop, $data);

                return $order;
            });

            return [
                'status' => true,
                'message' => ResponseError::NO_ERROR,
                'data' => $order->fresh([
                    'shop',
                    'coupon',
                    'orderDetails' => fn($q) => $q->whereNull('parent_id'),
                    'orderDetails.stock.countable.discounts' => fn($q) => $q->where('start', '<=', today())
                        ->where('end', '>=', today())
                        ->where('active', 1),
                    'orderDetails.stock.countable:id,status,shop_id,min_qty,max_qty,tax,img',
                    'orderDetails.children.stock.countable:id,status,shop_id,min_qty,max_qty,tax',
                ])
            ];

        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'    => false,
                'message'   => __('errors.' . ResponseError::ERROR_502, locale: $this->language),
                'code'      => ResponseError::ERROR_502
            ];
        }
    }

    /**
     * @param Order $order
     * @param Shop|null $shop
     * @param array $data
     * @return void
     * @throws Exception
     */
    private function calculateOrder(Order $order, ?Shop $shop, array $data): void
    {
        /** @var Order $order */
        $order = $order->fresh(['orderDetails']);

        $totalPrice     = $order->orderDetails->sum('total_price');
        $totalDiscount  = $order->orderDetails->sum('discount');

        $shopTax        = max($totalPrice / 100 * $shop?->tax, 0);
        $totalPrice     += ($order->delivery_fee + $shopTax);

        $coupon = Coupon::checkCoupon(data_get($data, 'coupon'))->first();

        if ($coupon) {
            $totalPrice -= $this->checkCoupon($coupon, $order, $totalPrice);
        }

        $totalDiscount += $this->recalculateReceipt($order);

        $isSubscribe = (int)Settings::adminSettings()->where('key', 'by_subscription')->first()?->value;

        $totalPrice = max(max($totalPrice, 0) - max($totalDiscount, 0), 0);

        $commissionFee = !$isSubscribe ?
            max(($totalPrice / 100 * $shop?->percentage <= 0.99 ? 1 : $shop?->percentage), 0)
            : 0;

        $order->update([
            'total_price'       => $totalPrice,
            'commission_fee'    => $commissionFee,
            'total_discount'    => max($totalDiscount, 0),
            'tax'               => $shopTax
        ]);

        $isSubscribe = (int)Settings::adminSettings()->where('key', 'by_subscription')->first()?->value;

        if ($isSubscribe) {

            $orderLimit = $order->shop?->subscription?->subscription?->order_limit;

            $shopOrdersCount = DB::table('orders')->select(['deleted_at', 'shop_id'])
                ->whereNull('deleted_at')
                ->where('shop_id', $order->shop_id)
                ->count('shop_id');

            if ($orderLimit < $shopOrdersCount) {
                $shop?->update([
                    'visibility' => 0
                ]);
            }

        }
    }
    /**
     * @param Coupon $coupon
     * @param Order $order
     * @param $totalPrice
     * @return float|int|null
     */
    private function checkCoupon(Coupon $coupon, Order $order, $totalPrice): float|int|null
    {
        if ($coupon->qty <= 0) {
            return 0;
        }

        $couponPrice = $coupon->type === 'percent' ? ($totalPrice / 100) * $coupon->price : $coupon->price;

        $order->coupon()->create([
            'user_id'   => $order->user_id,
            'name'      => $coupon->name,
            'price'     => $couponPrice,
        ]);

        $coupon->decrement('qty');

        return $couponPrice;
    }

    /**
     * @param int|null $orderId
     * @param int $deliveryman
     * @param int|null $shopId
     * @return array
     */
    public function updateDeliveryMan(?int $orderId, int $deliveryman, ?int $shopId = null): array
    {
        try {
            /** @var Order $order */
            $order = Order::when($shopId, fn($q) => $q->where('shop_id', $shopId))->find($orderId);

            if (!$order) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_404,
                    'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
                ];
            }

            if ($order->delivery_type === Order::PICKUP) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_502,
                    'message' => __('errors.' . ResponseError::ORDER_PICKUP, locale: $this->language)
                ];
            }

            /** @var User $user */
            $user = User::with('deliveryManSetting')->find($deliveryman);

            if (!$user || !$user->hasRole('deliveryman')) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_211,
                    'message' => __('errors.' . ResponseError::ERROR_211, locale: $this->language)
                ];
            }

            if (!$user->invitations?->where('shop_id', $order->shop_id)?->first()?->id) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_212,
                    'message' => __('errors.' . ResponseError::ERROR_212, locale: $this->language)
                ];
            }

            $order->update([
                'deliveryman' => $user->id,
            ]);

            $this->sendNotification(
                is_array($user->firebase_token) ? $user->firebase_token : [$user->firebase_token],
                __('errors.' . ResponseError::NEW_ORDER, ['id' => $order->id], $this->language),
                $order->id,
                (new NotificationHelper)->deliveryManOrder($order, $this->language, 'new_order')
            );

            return [
                'status'    => true,
                'message'   => ResponseError::NO_ERROR,
                'data'      => $order,
                'user'      => $user
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function attachDeliveryMan(?int $id): array
    {
        try {
            /** @var Order $order */
            $order = Order::with('user')->find($id);

            if ($order->delivery_type === Order::PICKUP) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_502,
                    'message' => __('errors.' . ResponseError::ORDER_PICKUP, locale: $this->language)
                ];
            }

            if (!empty($order->deliveryman)) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_210,
                    'message'   => __('errors.' . ResponseError::ERROR_210, locale: $this->language)
                ];
            }

            if (!auth('sanctum')->user()?->invitations?->where('shop_id', $order->shop_id)?->first()?->id) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_212,
                    'message'   => __('errors.' . ResponseError::ERROR_212, locale: $this->language)
                ];
            }

            $order->update([
                'deliveryman' => auth('sanctum')->id(),
            ]);

            return ['status' => true, 'message' => ResponseError::NO_ERROR, 'data' => $order];
        } catch (Throwable) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    /**
     * @param array $data
     * @param Shop $shop
     * @return array
     */
    private function setOrderParams(array $data, Shop $shop): array
    {
        $defaultCurrencyId = Currency::whereDefault(1)->first('id');

        $currencyId  = data_get($data, 'currency_id', data_get($defaultCurrencyId, 'id'));

        $helper      = new Utility;
        $km          = $helper->getDistance($shop->location, data_get($data, 'location'));

        $deliveryFee = $helper->getPriceByDistance($km, $shop, (float)data_get($data, 'rate', 1));

        $deliveryFeeRate = data_get($data, 'delivery_type') === Order::DELIVERY ?
            $deliveryFee / data_get($data, 'rate') : 0;

        return [
            'user_id'               => data_get($data, 'user_id', auth('sanctum')->id()),
            'total_price'           => 0,
            'currency_id'           => $currencyId,
            'rate'                  => data_get($data, 'rate'),
            'note'                  => data_get($data, 'note'),
            'shop_id'               => data_get($data, 'shop_id'),
            'phone'                 => data_get($data, 'phone'),
            'username'              => data_get($data, 'username'),
            'tax'                   => 0,
            'commission_fee'        => 0,
            'status'                => data_get($data, 'status', 'new'),
            'delivery_fee'          => max($deliveryFeeRate, 0),
            'delivery_type'         => data_get($data, 'delivery_type'),
            'location'              => data_get($data, 'location'),
            'address'               => data_get($data, 'address'),
            'deliveryman'           => data_get($data, 'deliveryman'),
            'delivery_date'         => data_get($data, 'delivery_date'),
            'delivery_time'         => data_get($data, 'delivery_time'),
            'total_discount'        => 0,
        ];
    }

    /**
     * @param array|null $ids
     * @param int|null $shopId
     * @return array
     */
    public function destroy(?array $ids = [], ?int $shopId = null): array
    {
        $errors = [];

        foreach (Order::when($shopId, fn($q) => $q->where('shop_id', $shopId))->find(is_array($ids) ? $ids : []) as $order) {
            try {
                $order->delete();
            } catch (Throwable $e) {
                $errors[] = $order->id;

                $this->error($e);
            }
        }

        return $errors;
    }

    /**
     * @param int $id
     * @param int|null $userId
     * @return array
     */
    public function setCurrent(int $id, ?int $userId = null): array
    {
        $errors = [];

        $orders = Order::when($userId, fn($q) => $q->where('deliveryman', $userId))
            ->where('current', 1)
            ->orWhere('id', $id)
            ->get();

        $getOrder = new Order;

        foreach ($orders as $order) {

            try {

                if ($order->id === $id) {

                    $order->update([
                        'current' => true,
                    ]);

                    $getOrder = $order;

                    continue;

                }

                $order->update([
                    'current' => false,
                ]);

            } catch (Throwable $e) {
                $errors[] = $order->id;

                $this->error($e);
            }

        }

        return count($errors) === 0 ? [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
            'data' => $getOrder
        ] : [
            'status'  => false,
            'code'    => ResponseError::ERROR_400,
            'message' => __(
                'errors.' . ResponseError::CANT_UPDATE_ORDERS,
                [
                    'ids' => implode(', #', $errors)
                ],
                $this->language
            )
        ];
    }

    /**
     * @param Order $order
     * @return int|float
     */
    public function recalculateReceipt(Order $order): int|float
    {
        $inReceipts = $order->orderDetails
            ?->where('bonus', 0)
            ?->pluck('quantity', 'stock_id')
            ?->toArray();

        foreach ($order->orderDetails as $orderDetail) {

            if (empty($orderDetail?->stock)) {
                $orderDetail->delete();
                continue;
            }

            if (!$orderDetail->bonus) {
                $inReceipts[$orderDetail->stock_id] = $orderDetail->quantity;
            }

        }

        /** @var Receipt|null $receipt */
        $receipts = Receipt::with('stocks')
            ->whereHas('stocks')
            ->where('shop_id', $order->shop_id)
            ->get();

        return (new CartService)->receipts($receipts, $inReceipts);
    }
}
