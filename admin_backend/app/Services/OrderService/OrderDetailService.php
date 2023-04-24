<?php

namespace App\Services\OrderService;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Stock;
use App\Services\CoreService;

class OrderDetailService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrderDetail::class;
    }

    public function create(Order $order, array $collection): Order
    {
        foreach ($order->orderDetails as $orderDetail) {

            $orderDetail?->stock?->increment('quantity', $orderDetail?->quantity);

            $orderDetail?->forceDelete();

        }

        $addons = [];

        foreach ($collection as $item) {

            if (!empty(data_get($item, 'parent_id'))) {
                $addons[] = $item;
                continue;
            }

            /** @var Stock $stock */
            $stock = Stock::with([
                'countable:id,status,shop_id,min_qty,max_qty,tax',
                'countable.discounts' => fn($q) => $q
                    ->where('start', '<=', today())
                    ->where('end', '>=', today())
                    ->where('active', 1)
            ])
                ->find(data_get($item, 'stock_id'));

            if (empty($stock)) {
                continue;
            }

            $actualQuantity = $this->actualQuantity($stock, data_get($item, 'quantity', 0));

            if (empty($actualQuantity) || $actualQuantity <= 0) {
                continue;
            }

            data_set($item, 'quantity', $actualQuantity);

            /** @var OrderDetail $parent */
            $parent = OrderDetail::where([
                ['stock_id', $stock->id],
                ['order_id', $order->id]
            ])->first();

            $item['parent_id'] = $parent?->id;

            $order->orderDetails()->create($this->setItemParams($item, $stock));

            $stock->decrement('quantity', $actualQuantity);
        }

        foreach ($addons as $addon) {

            /** @var Stock $stock */
            $stock = Stock::with([
                'countable:id,status,shop_id,min_qty,max_qty,tax',
                'countable.discounts' => fn($q) => $q
                    ->where('start', '<=', today())
                    ->where('end', '>=', today())
                    ->where('active', 1)
            ])
                ->find(data_get($addon, 'stock_id'));

            if (!$stock) {
                continue;
            }

            $actualQuantity = $this->actualQuantity($stock, data_get($addon, 'quantity', 0));

            if (empty($actualQuantity) || $actualQuantity <= 0) {
                continue;
            }

            $addon['quantity'] = $actualQuantity;

            $parent = OrderDetail::where([
                ['stock_id', data_get($addon, 'parent_id')],
                ['order_id', $order->id]
            ])->first();

            $addon['parent_id'] = $parent?->id;

            $order->orderDetails()->create($this->setItemParams($addon, $stock));

            $stock->decrement('quantity', $actualQuantity);
        }

        return $order;

    }

    public function createOrderUser(Order $order, int $cartId): Order
    {
        /** @var Cart $cart */
        $cart = Cart::with([
            'userCarts.cartDetails.stock.countable:id,status,shop_id,min_qty,max_qty,tax',
            'userCarts.cartDetails.stock.countable.discounts' => fn($q) => $q
                ->where('start', '<=', today())
                ->where('end', '>=', today())
                ->where('active', 1),
        ])->find($cartId);

        if (empty($cart?->userCarts)) {
            return $order;
        }

        foreach ($cart->userCarts as $userCart) {

            $cartDetails = $userCart->cartDetails;

            if (empty($cartDetails)) {
                $userCart->delete();
                continue;
            }

            $addons = [];

            foreach ($cartDetails as $cartDetail) {

                if (!empty($cartDetail?->parent_id)) {
                    $addons[] = $cartDetail;
                    continue;
                }

                $stock = $cartDetail->stock;

                if (!$stock) {
                    $cartDetail->delete();
                    continue;
                }

                $actualQuantity = $this->actualQuantity($stock, $cartDetail->quantity);

                if (empty($actualQuantity) || $actualQuantity <= 0) {
                    $cartDetail->delete();
                    continue;
                }

                $cartDetail->setAttribute('quantity', $actualQuantity);

                $order->orderDetails()->create($this->setItemParams($cartDetail, $stock));

                $stock->decrement('quantity', $actualQuantity);
            }

            foreach ($addons as $addon) {

                $stock = $addon->stock;

                if (!$stock) {
                    $addon->forceDelete();
                    continue;
                }

                $actualQuantity = $this->actualQuantity($stock, $addon->quantity);

                if (empty($actualQuantity) || $actualQuantity <= 0) {
                    $addon->forceDelete();
                    continue;
                }

                $addon->setAttribute('quantity', $actualQuantity);

                $parent = OrderDetail::where([
                    ['stock_id', $addon->parent?->stock_id],
                    ['order_id', $order->id]
                ])->first();

                $addon->setAttribute('parent_id', $parent?->id);

                $order->orderDetails()->create($this->setItemParams($addon, $stock));

                $stock->decrement('quantity', $actualQuantity);
            }

        }

        $cart->delete();

        return $order;

    }

    private function setItemParams($item, ?Stock $stock): array
    {

        $quantity = data_get($item, 'quantity', 0);

        if (data_get($item, 'bonus')) {

            data_set($item, 'origin_price', 0);
            data_set($item, 'total_price', 0);
            data_set($item, 'tax', 0);
            data_set($item, 'discount', 0);

        } else {

            $originPrice = $stock?->price * $quantity;

            $discount    = $stock?->actual_discount * $quantity;

            $tax         = $stock?->tax_price * $quantity;

            $totalPrice  = $originPrice - $discount + $tax;

            data_set($item, 'origin_price', $originPrice);
            data_set($item, 'total_price', max($totalPrice,0));
            data_set($item, 'tax', $tax);
            data_set($item, 'discount', $discount);
        }

        return [
            'origin_price'  => data_get($item, 'origin_price', 0),
            'tax'           => data_get($item, 'tax', 0),
            'discount'      => data_get($item, 'discount', 0),
            'total_price'   => data_get($item, 'total_price', 0),
            'stock_id'      => data_get($item, 'stock_id'),
            'parent_id'     => data_get($item, 'parent_id'),
            'quantity'      => $quantity,
            'bonus'         => data_get($item, 'bonus', false)
        ];
    }

    /**
     * @param Stock|null $stock
     * @param $quantity
     * @return mixed
     */
    private function actualQuantity(?Stock $stock, $quantity): mixed
    {

        $countable = $stock?->countable;

        if ($quantity < ($countable?->min_qty ?? 0)) {

            $quantity = $countable?->min_qty;

        } else if($quantity > ($countable?->max_qty ?? 0)) {

            $quantity = $countable?->max_qty;

        }

        return $quantity > $stock->quantity ? max($stock->quantity, 0) : $quantity;
    }

}
