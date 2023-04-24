<?php

namespace App\Http\Resources\Booking;

use App\Http\Resources\UserResource;
use App\Models\Booking\UserBooking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookingResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var UserBooking|JsonResource $this */

        return [
            'id'            => $this->when($this->id,         $this->id),
            'booking_id'    => $this->when($this->booking_id, $this->booking_id),
            'user_id'       => $this->when($this->user_id,    $this->user_id),
            'table_id'      => $this->when($this->table_id,   $this->table_id),
            'start_date'    => $this->when($this->start_date, $this->start_date),
            'end_date'      => $this->when($this->end_date,   $this->end_date),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'booking'       => BookingResource::make($this->whenLoaded('booking')),
            'user'          => UserResource::make($this->whenLoaded('user')),
            'table'         => TableResource::make($this->whenLoaded('table')),
        ];
    }
}
