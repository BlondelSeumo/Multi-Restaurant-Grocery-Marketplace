<?php

namespace App\Http\Resources;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleDiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Discount|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'shop_id'       => $this->when($this->shop_id, $this->shop_id),
            'type'          => $this->when($this->type, $this->type),
            'price'         => $this->when($this->price, $this->price),
            'start'         => $this->when($this->start, $this->start),
            'end'           => $this->when($this->end, $this->end),
            'active'        => $this->when($this->active, $this->active),
            'img'           => $this->when($this->img, $this->img),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
