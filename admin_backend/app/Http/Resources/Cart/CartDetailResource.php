<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\StockResource;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var CartDetail|JsonResource $this */
        return [
            'id'            => $this->id,
            'quantity'      => $this->quantity,
            'bonus'         => $this->bonus,
            'price'         => $this->rate_price,
            'discount'      => $this->rate_discount,
            'updated_at'    => $this->updated_at,
            'stock'         => StockResource::make($this->whenLoaded('stock')),
            'parent'        => CartDetailResource::make($this->whenLoaded('parent')),
            'addons'        => CartDetailResource::collection($this->whenLoaded('children')),
        ];
    }
}
