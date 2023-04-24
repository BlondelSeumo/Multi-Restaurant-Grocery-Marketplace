<?php

namespace App\Http\Resources\Booking;

use App\Http\Resources\GalleryResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\TranslationResource;
use App\Models\Booking\ShopSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopSectionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopSection|JsonResource $this */
        $locales = $this->relationLoaded('translations') ?
            $this->translations->pluck('locale')->toArray() : null;

        return [
            'id'            => $this->when($this->id,           $this->id),
            'shop_id'       => $this->when($this->shop_id,      $this->shop_id),
            'area'          => $this->when($this->area,         $this->area),
            'img'           => $this->when($this->img,          $this->img),
            'created_at'    => $this->when($this->created_at,   $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at,   $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at,   $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'shop'          => ShopResource::make($this->whenLoaded('shop')),
            'translation'   => TranslationResource::make($this->whenLoaded('translation')),
            'translations'  => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'       => $this->when($locales, $locales),
            'gallery'       => GalleryResource::make($this->whenLoaded('gallery')),
            'galleries'     => GalleryResource::collection($this->whenLoaded('galleries')),
        ];
    }
}
