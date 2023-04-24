<?php

namespace App\Http\Resources;

use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Referral|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'price_from'    => $this->price_from,
            'price_to'      => $this->price_to,
            'img'           => $this->img,
            'expired_at'    => $this->when($this->expired_at, $this->expired_at),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'translation'   => TranslationResource::make($this->whenLoaded('translation')),
            'translations'  => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'       => $this->whenLoaded('translations', $this->translations->pluck('locale')->toArray()),
        ];
    }
}
