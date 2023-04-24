<?php

namespace App\Http\Resources;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var OrderStatus|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'name'          => $this->when($this->name, $this->name),
            'active'        => (bool)$this->active,
            'sort'          => $this->when($this->sort, $this->sort),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
