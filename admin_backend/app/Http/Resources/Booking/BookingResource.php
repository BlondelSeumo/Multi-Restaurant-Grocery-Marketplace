<?php

namespace App\Http\Resources\Booking;

use App\Http\Resources\ShopResource;
use App\Models\Booking\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Booking|JsonResource $this */

        return [
            'id'            => $this->when($this->id,           $this->id),
            'max_time'      => $this->when($this->max_time,     $this->max_time),
            'start_time'    => $this->when($this->start_time,   $this->start_time),
            'end_time'      => $this->when($this->end_time,     $this->end_time),
            'created_at'    => $this->when($this->created_at,   $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at,   $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at,   $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'shop'          => ShopResource::make($this->whenLoaded('shop')),
        ];
    }
}
