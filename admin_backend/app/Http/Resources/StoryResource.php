<?php

namespace App\Http\Resources;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Story|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'file_urls'     => $this->when($this->file_urls, $this->file_urls),
            'created_at'    => $this->when($this->created_at, optional($this->created_at)->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, optional($this->updated_at)->format('Y-m-d H:i:s')),

            //Relations
            'product'       => ProductResource::make($this->whenLoaded('product')),
            'shop'          => ShopResource::make($this->whenLoaded('shop')),
        ];
    }
}
