<?php

namespace App\Http\Resources;

use App\Models\SmsPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsPayloadResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var SmsPayload|JsonResource $this */
        return [
            'type'          => $this->when($this->type, $this->type),
            'payload'       => $this->when($this->payload, $this->payload),
            'default'       => (bool)$this->default,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
