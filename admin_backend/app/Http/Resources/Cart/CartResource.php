<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\ShopResource;
use App\Http\Resources\UserResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Cart|JsonResource $this */
        return [
            'id'                => $this->id,
            'owner_id'          => $this->owner_id,

            'shop_id'           => $this->shop_id,
            'status'            => $this->status,
            'total_price'       => $this->rate_total_price,
            'receipt_discount'  => $this->when(request('receipt_discount'), request('receipt_discount') * $this->rate),
            'receipt_count'     => $this->when(request('receipt_count'), request('receipt_count')),
            'currency_id'       => $this->currency_id,
            'rate'              => $this->rate,
            'group'             => (bool)$this->group,
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'user'              => UserResource::make($this->whenLoaded('user')),
            'shop'              => ShopResource::make($this->whenLoaded('shop')),
            'user_carts'        => UserCartResource::collection($this->whenLoaded('userCarts')),
        ];
    }
}
