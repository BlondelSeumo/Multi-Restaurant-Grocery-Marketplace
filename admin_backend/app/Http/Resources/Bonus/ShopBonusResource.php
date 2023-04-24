<?php

namespace App\Http\Resources\Bonus;

use App\Http\Resources\StockResource;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopBonusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Bonus|JsonResource $this */
        return [
            'id'                => $this->when($this->id, $this->id),
            'bonusable_type'    => $this->when($this->bonusable_type, $this->bonusable_type),
            'bonusable_id'      => $this->when($this->bonusable_id, $this->bonusable_id),
            'bonus_quantity'    => $this->when($this->bonus_quantity, $this->bonus_quantity),
            'bonus_stock_id'    => $this->when($this->bonus_stock_id, $this->bonus_stock_id),
            'value'             => $this->when($this->value, $this->value),
            'type'              => $this->when($this->type, $this->type),
            'status'            => (boolean)$this->status,
            'expired_at'        => $this->when($this->expired_at, $this->expired_at->format('Y-m-d')),
            'bonusStock'        => StockResource::make($this->whenLoaded('stock')),
        ];
    }
}
