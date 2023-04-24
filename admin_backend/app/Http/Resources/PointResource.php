<?php

namespace App\Http\Resources;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Point|JsonResource $this */
        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'price'         => $this->price,
            'value'         => $this->value,
            'active'        => (bool)$this->active,
            'shop'          => ShopResource::make($this->whenLoaded('shop')),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];

    }
}
