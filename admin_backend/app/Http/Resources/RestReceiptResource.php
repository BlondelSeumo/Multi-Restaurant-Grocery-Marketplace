<?php

namespace App\Http\Resources;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestReceiptResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Receipt|JsonResource $this */

        $type = data_get(Receipt::DISCOUNT_TYPE_VALUES, $this->discount_type);

        $originPrice    = 0;
        $discountPrice  = $this->discount_price;

        if ($this->relationLoaded('stocks')) {

            $originPrice += $this->stocks->reduce(fn($c, $i) => $c + ($i?->total_price * ($i?->pivot?->min_quantity ?? 1)));

            if ($this->discount_type === 1) {
                $discountPrice = $originPrice / 100 * $this->discount_price;
            }

        }

        return [
            'id'                => $this->when($this->id, $this->id),
            'shop_id'           => $this->when($this->shop_id, $this->shop_id),
            'img'               => $this->when($this->img, $this->img),
            'category_id'       => $this->when($this->category_id, $this->category_id),
            'active_time'       => $this->when($this->active_time, $this->active_time),
            'total_time'        => $this->when($this->total_time, $this->total_time),
            'calories'          => $this->when($this->calories, $this->calories),
            'servings'          => $this->when($this->servings, $this->servings),
            'discount_type'     => $this->when($type, $type),
            'discount_price'    => $this->when($discountPrice, $discountPrice * $this->currency()),
            'origin_price'      => $this->when($originPrice, $originPrice * $this->currency()),
            'total_price'       => $this->when($originPrice && $discountPrice, ($originPrice - $discountPrice) * $this->currency()),
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'shop'                  => ShopResource::make($this->whenLoaded('shop')),
            'category'              => CategoryResource::make($this->whenLoaded('category')),
            'translation'           => TranslationResource::make($this->whenLoaded('translation')),
            'ingredient'            => TranslationResource::make($this->whenLoaded('ingredient')),
            'instruction'           => TranslationResource::make($this->whenLoaded('instruction')),
            'nutritions'            => ReceiptNutritionResource::collection($this->whenLoaded('nutritions')),
            'galleries'             => GalleryResource::collection($this->whenLoaded('galleries')),
            'stocks'                => StockResource::collection($this->whenLoaded('stocks')),
        ];
    }
}
