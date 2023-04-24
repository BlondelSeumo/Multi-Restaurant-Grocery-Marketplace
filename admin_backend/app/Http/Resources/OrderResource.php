<?php

namespace App\Http\Resources;

use App\Helpers\Utility;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Order|JsonResource $this */

        $order = $this;
        $location = 0;

        if ($this->relationLoaded('shop')) {

            $shopLocation = [];
            $orderLocation = [];

            if (data_get($order->shop, 'location.latitude') && data_get($order->shop, 'location.longitude')) {
                $shopLocation = data_get($order->shop, 'location');
            }

            if (data_get($order->location, 'latitude') && data_get($order->location, 'longitude')) {
                $orderLocation = $order->location;
            }

            if (count($shopLocation) === 2 && count($orderLocation) === 2) {
                $location = (new Utility)->getDistance($shopLocation, $orderLocation);
            }

        }

        $couponPrice = 0;

        if ($this->relationLoaded('coupon')) {

            $couponPrice = $this->coupon?->price;

        }

        return [
            'id'                            => $this->when($this->id, $this->id),
            'user_id'                       => $this->when($this->user_id, $this->user_id),
            'total_price'                   => $this->when($this->rate_total_price, $this->rate_total_price),
            'origin_price'                  => $this->when($this->rate_total_price, $this->rate_total_price + $this->rate_total_discount - $this->rate_tax - $this->rate_delivery_fee + $couponPrice),
            'rate'                          => $this->when($this->rate, $this->rate),
            'note'                          => $this->when(isset($this->note), (string) $this->note),
            'order_details_count'           => $this->when($this->order_details_count, (int) $this->order_details_count),
            'order_details_sum_quantity'    => $this->when($this->order_details_sum_quantity, $this->order_details_sum_quantity),
            'tax'                           => $this->when($this->rate_tax, $this->rate_tax),
            'commission_fee'                => $this->when($this->rate_commission_fee, $this->rate_commission_fee),
            'status'                        => $this->when($this->status, $this->status),
            'location'                      => $this->when($this->location, $this->location),
            'address'                       => $this->when($this->address, $this->address),
            'delivery_type'                 => $this->when($this->delivery_type, $this->delivery_type),
            'delivery_fee'                  => $this->when($this->rate_delivery_fee, $this->rate_delivery_fee),
            'deliveryman'                   => UserResource::make($this->whenLoaded('deliveryMan')),
            'delivery_date'                 => $this->when($this->delivery_date, $this->delivery_date),
            'delivery_time'                 => $this->when($this->delivery_time, $this->delivery_time),
            'phone'                         => $this->when($this->phone, $this->phone),
            'username'                      => $this->when($this->username, $this->username),
            'receipt_discount'              => $this->when(request('receipt_discount'), request('receipt_discount') * $this->rate),
            'receipt_count'                 => $this->when(request('receipt_count'), request('receipt_count')),
            'current'                       => (bool)$this->current,
            'img'                           => $this->when($this->img, $this->img),
            'total_discount'                => $this->when($this->rate_total_discount, $this->rate_total_discount),
            'created_at'                    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'                    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'                    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
            'km'                            => $this->whenLoaded('shop', $location),
            'shop'                          => ShopResource::make($this->whenLoaded('shop')),
            'currency'                      => CurrencyResource::make($this->whenLoaded('currency')),
            'user'                          => UserResource::make($this->whenLoaded('user')),
            'details'                       => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
            'transaction'                   => TransactionResource::make($this->whenLoaded('transaction')),
            'transactions'                  => TransactionResource::collection($this->whenLoaded('transactions')),
            'review'                        => ReviewResource::make($this->whenLoaded('review')),
            'reviews'                       => ReviewResource::collection($this->whenLoaded('reviews')),
            'point_histories'               => PointResource::collection($this->whenLoaded('pointHistories')),
            'order_refunds'                 => OrderRefundResource::collection($this->whenLoaded('orderRefunds')),
            'coupon'                        => CouponResource::make($this->whenLoaded('coupon')),
            'galleries'                     => GalleryResource::collection($this->whenLoaded('galleries')),
        ];
    }
}
