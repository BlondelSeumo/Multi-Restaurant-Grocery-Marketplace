<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Subscription|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'type'          => $this->when($this->type, $this->type),
            'price'         => $this->when($this->price, $this->price),
            'month'         => $this->when($this->month, $this->month),
            'active'        => $this->when($this->active, $this->active),
            'title'         => $this->when($this->title, $this->title),
            'product_limit' => $this->when($this->product_limit, $this->product_limit),
            'order_limit'   => $this->when($this->order_limit, $this->order_limit),
            'with_report'   => $this->when($this->with_report, $this->with_report),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
