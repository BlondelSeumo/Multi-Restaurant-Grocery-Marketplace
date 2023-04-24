<?php

namespace App\Http\Resources;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Translation|JsonResource $this */
        return [
            'id' => (int) $this->id,
            'group' => (string) $this->group,
            'key' => (string) $this->key,
            'value' => [
                'locale' => (string) $this->locale,
                'value' => (string) $this->value,
            ],
            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at'    => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
