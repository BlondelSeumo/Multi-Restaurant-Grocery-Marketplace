<?php

namespace App\Http\Resources;

use App\Models\ShopSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopSubscription|JsonResource $this */
        return [
            'id'                => $this->when($this->id, $this->id),
            'shop_id'           => $this->when($this->shop_id, $this->shop_id),
            'subscription_id'   => $this->when($this->subscription_id, $this->subscription_id),
            'expired_at'        => $this->when($this->expired_at, $this->expired_at),
            'price'             => $this->when($this->price, $this->price),
            'type'              => $this->when($this->type, $this->type),
            'active'            => $this->when($this->active, $this->active),
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
            'subscription'      => SubscriptionResource::make($this->whenLoaded('subscription')),
        ];
    }
}
