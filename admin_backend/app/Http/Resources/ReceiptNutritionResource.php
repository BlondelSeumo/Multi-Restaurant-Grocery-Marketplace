<?php

namespace App\Http\Resources;

use App\Models\ReceiptNutrition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptNutritionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ReceiptNutrition|JsonResource $this */

        $locales = $this->relationLoaded('translations') ?
            $this->translations->pluck('locale')->toArray() : null;

        return [
            'id'                => $this->id,
            'weight'            => $this->when($this->weight, $this->weight),
            'percentage'        => $this->when($this->percentage, $this->percentage),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            'translation'       => TranslationResource::make($this->whenLoaded('translation')),
            'translations'      => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'           => $this->when($locales, $locales),
        ];
    }
}
