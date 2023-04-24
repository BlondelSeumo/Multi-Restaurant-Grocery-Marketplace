<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleStockReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Stock|JsonResource $this */
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(request('date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(request('date_to', now())));

        return [
            'order_quantity' => $this->orderDetails->where('order.status', Order::STATUS_DELIVERED)
                    ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                    ->where('order.created_at', '>=', $dateFrom)
                    ->where('order.created_at', '<=', $dateTo)
                    ->sum('quantity') ?? 0,
            'quantity' => $this->quantity ?? 0,
            'count'    => $this->orderDetails->where('order.status', Order::STATUS_DELIVERED)
                ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                ->where('order.created_at', '>=', $dateFrom)
                ->where('order.created_at', '<=', $dateTo)
                ->groupBy('order_id')->count(),
            'price' => $this->orderDetails->where('order.status', Order::STATUS_DELIVERED)
                    ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                    ->where('order.created_at', '>=', $dateFrom)
                    ->where('order.created_at', '<=', $dateTo)
                    ->groupBy('order_id')->reduce(fn($carry, $item) =>
                $carry + $item->groupBy('order_id')->reduce(fn($c, $i) => $c + $i->sum('order.total_price'))
            ) ?? 0,
            // Relation
            'extras' => ExtraValueResource::collection($this->stockExtras),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
