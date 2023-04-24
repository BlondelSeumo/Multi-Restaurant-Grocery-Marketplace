<?php

namespace App\Http\Resources;

use App\Models\PaymentPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentPayloadResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var PaymentPayload|JsonResource $this */
        return [
            'payment_id'    => $this->when($this->payment_id, $this->payment_id),
            'payload'       => $this->when($this->payload, $this->payload),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            //Relations
            'payment'       => PaymentResource::make($this->whenLoaded('payment')),
        ];
    }
}
