<?php

namespace App\Http\Resources;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Wallet|JsonResource $this */
        return [
            'uuid'          => (string) $this->uuid,
            'user_id'       => (int) $this->user_id,
            'price'         => $this->price_rate,
            'symbol'        => (string) $this->symbol,
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'histories'     => WalletHistoryResource::collection($this->whenLoaded('histories')),
            'currency'      => CurrencyResource::make($this->whenLoaded('currency')),
        ];
    }
}
