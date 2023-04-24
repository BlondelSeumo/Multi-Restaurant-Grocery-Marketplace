<?php

namespace App\Http\Resources;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Language|JsonResource $this */
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'locale'        => $this->locale,
            'backward'      => $this->backward,
            'default'       => $this->default,
            'active'        => $this->active,
            'img'           => $this->img,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
