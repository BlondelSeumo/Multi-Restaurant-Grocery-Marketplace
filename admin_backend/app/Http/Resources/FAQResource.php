<?php

namespace App\Http\Resources;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Faq|JsonResource $this */
        return [
            'id'            => (int) $this->id,
            'uuid'          => (string) $this->uuid,
            'type'          => $this->when($this->type, (string) $this->type),
            'active'        => (bool)$this->active,
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'translation'   => TranslationResource::make($this->whenLoaded('translation')),
            'translations'  => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'       => $this->whenLoaded('translations', $this->translations->pluck('locale')->toArray()),
        ];
    }
}
