<?php

namespace App\Http\Resources;

use App\Models\ExtraValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ExtraValue|JsonResource $this */
        return [
            'id'                => (int) $this->id,
            'extra_group_id'    => (int) $this->extra_group_id,
            'value'             => (string) $this->value,
            'active'            => (boolean) $this->active,
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'group'             => ExtraGroupResource::make($this->whenLoaded('group')),
            'galleries'         => GalleryResource::collection($this->whenLoaded('galleries')),
        ];
    }
}
