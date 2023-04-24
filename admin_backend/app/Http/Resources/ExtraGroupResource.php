<?php

namespace App\Http\Resources;

use App\Models\ExtraGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ExtraGroup|JsonResource $this */
        return [
            'id'            => $this->id,
            'type'          => (string) $this->type,
            'active'        => (bool) $this->active,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relation
            'translation'   => TranslationResource::make($this->whenLoaded('translation')),
            'translations'  => TranslationResource::collection($this->whenLoaded('translations')),
            'extra_values'  => ExtraValueResource::collection($this->whenLoaded('extraValues')),
            'locales'       => $this->whenLoaded('translations', $this->translations->pluck('locale')->toArray()),
        ];
    }
}
