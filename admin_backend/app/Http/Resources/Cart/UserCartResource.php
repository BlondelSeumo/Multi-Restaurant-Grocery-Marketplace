<?php

namespace App\Http\Resources\Cart;

use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var UserCart|JsonResource $this */
        return [
            'id'            => $this->id,
            'cart_id'       => $this->cart_id,
            'user_id'       => $this->user_id,
            'status'        => (bool)$this->status,
            'name'          => $this->name,
            'uuid'          => $this->uuid,
            'cartDetails'   => CartDetailResource::collection($this->cartDetails),
        ];
    }
}
