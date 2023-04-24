<?php

namespace App\Http\Resources;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Blog|JsonResource $this */
        return [
            'id'            => (int) $this->id,
            'uuid'          => (string) $this->uuid,
            'user_id'       => $this->when($this->user_id, (string) $this->user_id),
            'type'          => $this->type,
            'published_at'  => $this->when(isset($this->published_at), (string) $this->published_at),
            'active'        => (bool)$this->active,
            'img'           => $this->when($this->img, $this->img),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'translation'   => TranslationResource::make($this->whenLoaded('translation')),
            'translations'  => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'       => $this->whenLoaded('translations', $this->translations->pluck('locale')->toArray()),
            'author'        => UserResource::make($this->whenLoaded('author')),
        ];
    }
}
