<?php

namespace App\Http\Resources;

use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var WalletHistory|JsonResource $this */
        return [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'wallet_uuid'   => $this->wallet_uuid,
            'transaction_id' => $this->transaction_id,
            'type'          => $this->type,
            'price'         => $this->price_rate,
            'note'          => $this->note,
            'status'        => $this->status,
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'author'        => UserResource::make($this->whenLoaded('author')),
            'user'          => UserResource::make($this->whenLoaded('user')),
            'transaction'   => TransactionResource::make($this->whenLoaded('transaction')),
        ];
    }
}
