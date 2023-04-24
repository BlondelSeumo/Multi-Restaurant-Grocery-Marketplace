<?php

namespace App\Http\Resources\Booking;

use App\Models\Booking\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Table|JsonResource $this */

        return [
            'id'                => $this->when($this->id,   $this->id),
            'name'              => $this->when($this->name, $this->name),
            'shop_section_id'   => $this->when($this->shop_section_id, $this->shop_section_id),
            'tax'               => $this->when($this->tax,         $this->tax),
            'chair_count'       => $this->when($this->chair_count, $this->chair_count),
            'created_at'        => $this->when($this->created_at,  $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at,  $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at,  $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'shop_section'      => ShopSectionResource::make($this->whenLoaded('shopSection')),
        ];
    }
}
