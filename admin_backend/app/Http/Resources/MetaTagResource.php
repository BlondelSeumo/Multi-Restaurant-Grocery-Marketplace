<?php

namespace App\Http\Resources;

use App\Models\MetaTag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MetaTagResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var MetaTag|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'path'          => $this->when($this->path, $this->path),
            'model_id'      => $this->when($this->model_id, $this->model_id),
            'model_type'    => $this->when($this->model_type, $this->model_type),
            'title'         => $this->when($this->title, $this->title),
            'keywords'      => $this->when($this->keywords, $this->keywords),
            'description'   => $this->when($this->description, $this->description),
            'h1'            => $this->when($this->h1, $this->h1),
            'seo_text'      => $this->when($this->seo_text, $this->seo_text),
            'canonical'     => $this->when($this->canonical, $this->canonical),
            'robots'        => $this->when($this->robots, $this->robots),
            'change_freq'   => $this->when($this->change_freq, $this->change_freq),
            'priority'      => $this->when($this->priority, $this->priority),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
