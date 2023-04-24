<?php

namespace App\Http\Resources;

use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopCategory|JsonResource $this */
        return [
            'id'            => $this->id,
            'shop_id'       => $this->shop_id,
            'category_id'   => $this->category_id,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
