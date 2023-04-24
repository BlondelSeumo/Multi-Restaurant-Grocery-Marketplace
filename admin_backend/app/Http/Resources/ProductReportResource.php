<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Product|JsonResource $this */
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(request('date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(request('date_to', now())));

        return [
            'id'            => $this->id,
            'bar_code'      => $this->bar_code,
            'category_id'   => $this->category_id,
            'active'        => $this->active,
            'shop_id'       => $this->shop_id,
            'quantity'      => $this->stocks->reduce(fn($carry, Stock $item) => $carry + $item->orderDetails
                        ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                        ->where('order.status', Order::STATUS_DELIVERED)
                        ->where('order.created_at', '>=', $dateFrom)
                        ->where('order.created_at', '<=', $dateTo)
                        ->sum('quantity')) ?? 0,
            'count' => $this->stocks->reduce(fn($carry, Stock $item) => $carry + $item->orderDetails
                        ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                    ->where('order.status', Order::STATUS_DELIVERED)
                    ->where('order.created_at', '>=', $dateFrom)
                    ->where('order.created_at', '<=', $dateTo)
                    ->groupBy('order_id')->count()
            ) ?? 0,
            'price' => $this->stocks->reduce(fn($carry, Stock $item) => $carry +
                $item->orderDetails
                    ->when(request('shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                    ->where('order.status', Order::STATUS_DELIVERED)
                    ->where('order.created_at', '>=', $dateFrom)
                    ->where('order.created_at', '<=', $dateTo)
                    ->groupBy('order_id')->reduce(fn($c, $i) => $c + $i->sum('order.total_price'))
            ) ?? 0,
            'deleted_at'    => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relations
            'translation'   => TranslationResource::make($this->translation),
            'stocks'        => SimpleStockReportResource::collection($this->stocks),
            'category'      => CategoryResource::make($this->category),
        ];
    }

}
