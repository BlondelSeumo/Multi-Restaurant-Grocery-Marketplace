<?php

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Brand|JsonResource $this */
        return [
            'id'                => (int) $this->id,
            'uuid'              => (string) $this->uuid,
            'title'             => (string) $this->title,
            'active'            => (bool)$this->active,
            'img'               => $this->when($this->img, $this->img),
            'products_count'    => $this->when($this->products_count, $this->products_count),
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            'meta_tags'         => MetaTagResource::collection($this->whenLoaded('metaTags')),
        ];
    }
}
