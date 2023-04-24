<?php

namespace App\Http\Resources;

use App\Models\ProductExtra;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductExtraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ProductExtra|JsonResource $this */
        return [
            'id'            => $this->id,
            'extra'         => ExtraValueResource::make($this->whenLoaded('extra')),
        ];
    }
}
