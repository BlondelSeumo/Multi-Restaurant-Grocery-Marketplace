<?php

namespace App\Repositories\OrderRepository;

use App\Models\Order;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SellerOrderReportRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param array $filter
     * @return array
     */
    public function report(array $filter = []): array
    {
        $type       = data_get($filter, 'type', 'day');
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(request('date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(request('date_to', now())));
        $now        = now()?->format('Y-m-d 00:00:01');

        $lastOrder  = Order::where('shop_id', data_get($filter, 'shop_id'))
            ->latest('id')
            ->first();

        $orders     = DB::table('orders')
            ->where('shop_id', data_get($filter, 'shop_id'))
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->whereNull('deleted_at')
            ->select([
                DB::raw("sum(if(status = 'delivered', total_price, 0)) as total_price"),
                DB::raw("sum(if(status = 'delivered', commission_fee, 0)) as commission_fee"),
                DB::raw("sum(if(status = 'delivered', delivery_fee, 0)) as delivery_fee"),
                DB::raw('count(id) as total_count'),
                DB::raw("sum(if(created_at >= '$now', 1, 0)) as total_today_count"),
                DB::raw("sum(if(status = 'new', 1, 0)) as total_new_count"),
                DB::raw("sum(if(status = 'ready', 1, 0)) as total_ready_count"),
                DB::raw("sum(if(status = 'on_a_way', 1, 0)) as total_on_a_way_count"),
                DB::raw("sum(if(status = 'accepted', 1, 0)) as total_accepted_count"),
                DB::raw("sum(if(status = 'canceled', 1, 0)) as total_canceled_count"),
                DB::raw("sum(if(status = 'delivered', 1, 0)) as total_delivered_count"),
            ])
            ->first();

        $fmTotalPrice  = data_get($orders, 'total_price', 0) -
            data_get($orders, 'commission_fee', 0) - data_get($orders, 'delivery_fee', 0);

        $type = match ($type) {
            'year' => '%Y',
            'week' => '%w',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $chart = DB::table('orders')
            ->where('shop_id', data_get($filter, 'shop_id'))
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->whereNull('deleted_at')
            ->where('status', Order::STATUS_DELIVERED)
            ->select([
                DB::raw("(DATE_FORMAT(created_at, '$type')) as time"),
                DB::raw('sum(total_price) as total_price'),
            ])
            ->groupBy('time')
            ->orderBy('time')
            ->get();

        return [
            'last_order_total_price'    => (int)ceil($lastOrder?->total_price) ?? 0,
            'last_order_income'         => (int)ceil($lastOrder?->seller_price) ?? 0,
            'total_price'               => (int)data_get($orders, 'total_price', 0),
            'fm_total_price'            => (int)$fmTotalPrice,
            'total_count'               => (int)data_get($orders, 'total_count', 0),
            'total_today_count'         => (int)data_get($orders, 'total_today_count', 0),
            'total_new_count'           => (int)data_get($orders, 'total_new_count', 0),
            'total_ready_count'         => (int)data_get($orders, 'total_ready_count', 0),
            'total_on_a_way_count'      => (int)data_get($orders, 'total_on_a_way_count', 0),
            'total_accepted_count'      => (int)data_get($orders, 'total_accepted_count', 0),
            'total_canceled_count'      => (int)data_get($orders, 'total_canceled_count', 0),
            'total_delivered_count'     => (int)data_get($orders, 'total_delivered_count', 0),
            'chart'                     => $chart
        ];
    }

    public function reportPaginate(array $filter = []): LengthAwarePaginator
    {
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(request('date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(request('date_to', now())));

        return Order::where('shop_id', data_get($filter, 'shop_id'))
            ->where('status', Order::STATUS_DELIVERED)
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->select([
                'status',
                'shop_id',
                'created_at',
                'total_price',
                'commission_fee',
                'delivery_fee',
            ])
            ->paginate(data_get($filter, 'perPage', 10));
    }

}
