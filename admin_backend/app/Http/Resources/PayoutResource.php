<?php

namespace App\Http\Resources;

use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Payout|JsonResource $this */

        return [
            'id'            => $this->when($this->id, $this->id),
            'status'        => $this->when($this->status, $this->status),
            'cause'         => $this->when($this->cause, $this->cause),
            'answer'        => $this->when($this->answer, $this->answer),
            'price'         => $this->when($this->price, $this->price),
            'createdBy'     => UserResource::make($this->whenLoaded('createdBy')),
            'approvedBy'    => UserResource::make($this->whenLoaded('approvedBy')),
            'currency'      => CurrencyResource::make($this->whenLoaded('currency')),
            'payment'       => PaymentResource::make($this->whenLoaded('payment')),
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
