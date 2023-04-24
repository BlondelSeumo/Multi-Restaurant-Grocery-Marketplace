<?php

namespace App\Http\Resources;

use App\Models\ShopPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopPayment|JsonResource $this */
        return [
            'id'            => $this->id,
            'shop_id'       => $this->shop_id,
            'status'        => $this->status,
            'client_id'     => $this->client_id,
            'secret_id'     => $this->secret_id,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
            'payment'       => PaymentResource::make($this->whenLoaded('payment'))
        ];
    }
}
