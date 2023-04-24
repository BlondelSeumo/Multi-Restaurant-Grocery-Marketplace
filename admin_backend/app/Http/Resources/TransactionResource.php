<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Transaction|JsonResource $this */
        return [
            'id'                    => $this->id,
            'payable_id'            => $this->payable_id,
            'price'                 => $this->price,
            'payment_trx_id'        => $this->payment_trx_id,
            'note'                  => $this->note,
            'perform_time'          => $this->when($this->perform_time, $this->perform_time),
            'refund_time'           => $this->when($this->refund_time, $this->refund_time),
            'status'                => $this->status,
            'status_description'    => $this->status_description,
            'created_at'            => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'            => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'            => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

            // Relations
            'user' => UserResource::make($this->whenLoaded('user')),
            'payment_system' => PaymentResource::make($this->whenLoaded('paymentSystem')),
            'payable' => $this->payable_type === Order::class ?
                OrderResource::make($this->whenLoaded('payable')) :
                WalletResource::make($this->whenLoaded('payable')),
        ];
    }
}
