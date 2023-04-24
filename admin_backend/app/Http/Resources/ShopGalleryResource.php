<?php

namespace App\Http\Resources;

use App\Models\ShopGallery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopGalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopGallery|JsonResource $this */
        return [
            'id'        => $this->when($this->id, $this->id),
            'active'    => $this->when($this->active, $this->active),
            'shop_id'   => $this->when($this->shop_id, $this->shop_id),
            'galleries' => GalleryResource::collection($this->whenLoaded('galleries')),
            'shop'      => ShopResource::make($this->whenLoaded('shop')),
        ];
    }
}
