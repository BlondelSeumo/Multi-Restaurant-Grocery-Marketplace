<?php

namespace App\Http\Resources;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
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

        $locales = $this->relationLoaded('translations') ?
            $this->translations->pluck('locale')->toArray() : null;

        $ingredients = $this->relationLoaded('ingredients') ?
            $this->ingredients->pluck('locale')->toArray() : null;

        $instructions = $this->relationLoaded('instructions') ?
            $this->instructions->pluck('locale')->toArray() : null;

        $type = data_get(Receipt::DISCOUNT_TYPE_VALUES, $this->discount_type);

        return [
            'id'                    => $this->when($this->id, $this->id),
            'shop_id'               => $this->when($this->shop_id, $this->shop_id),
            'img'                   => $this->when($this->img, $this->img),
            'discount_type'         => $this->when($type, $type),
            'discount_price'        => $this->when($this->discount_price, $this->discount_price),
            'category_id'           => $this->when($this->category_id, $this->category_id),
            'active_time'           => $this->when($this->active_time, $this->active_time),
            'total_time'            => $this->when($this->total_time, $this->total_time),
            'calories'              => $this->when($this->calories, $this->calories),
            'servings'              => $this->when($this->servings, $this->servings),
            'created_at'            => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'            => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'            => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'shop'                  => ShopResource::make($this->whenLoaded('shop')),
            'category'              => CategoryResource::make($this->whenLoaded('category')),
            'translation'           => TranslationResource::make($this->whenLoaded('translation')),
            'translations'          => TranslationResource::collection($this->whenLoaded('translations')),
            'ingredient'            => TranslationResource::make($this->whenLoaded('ingredient')),
            'ingredients'           => TranslationResource::collection($this->whenLoaded('ingredients')),
            'instruction'           => TranslationResource::make($this->whenLoaded('instruction')),
            'instructions'          => TranslationResource::collection($this->whenLoaded('instructions')),
            'nutritions'            => ReceiptNutritionResource::collection($this->whenLoaded('nutritions')),
            'locales'               => $this->when($locales, $locales),
            'ingredient_locales'    => $this->when($ingredients, $ingredients),
            'instruction_locales'   => $this->when($instructions, $instructions),
            'galleries'             => GalleryResource::collection($this->whenLoaded('galleries')),
            'stocks'                => StockResource::collection($this->whenLoaded('stocks')),
        ];
    }
}
