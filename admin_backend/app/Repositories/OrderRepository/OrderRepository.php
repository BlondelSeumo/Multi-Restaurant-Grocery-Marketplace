<?php

namespace App\Repositories\OrderRepository;

use App\Exports\OrdersReportExport;
use App\Exports\OrdersRevenueReportExport;
use App\Helpers\ResponseError;
use App\Helpers\Utility;
use App\Http\Resources\OrderResource;
use App\Models\Bonus;
use App\Models\CategoryTranslation;
use App\Models\Coupon;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Shop;
use App\Models\Stock;
use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\OrderRepoInterface;
use App\Repositories\ReportRepository\ChartRepository;
use App\Traits\SetCurrency;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class OrderRepository extends CoreRepository implements OrderRepoInterface
{
    use SetCurrency;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param array $filter
     * @return mixed
     */
    public function ordersList(array $filter = []): mixed
    {
        return $this->model()->with([
            'orderDetails.stock',
            'deliveryMan',
        ])
            ->updatedDate($this->updatedDate)
            ->filter($filter)
            ->get();
    }

    /**
     * This is only for users route
     * @param array $filter
     * @param string $paginate
     * @return Paginator
     */
    public function ordersPaginate(array $filter = [], string $paginate = 'simplePaginate'): Paginator
    {
        /** @var Order $order */
        $order = $this->model();

        return $order
            ->withCount('orderDetails')
            ->with([
                'shop:id,location,tax,price,price_per_km,background_img,logo_img',
                'shop.translation'      => fn($q) => $q->where('locale', $this->language),
                'currency'              => fn($q) => $q->select('id', 'title', 'symbol'),
            ])
            ->updatedDate($this->updatedDate)
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->$paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @return array
     */
    public function orderStocksCalculate(array $filter): array
    {
        $locale     = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');
        $products   = collect(data_get($filter, 'products', []));

        $result = [];

        /** @var Stock $stock */
        $stocks = Stock::with([
            'countable' => fn($q) => $q->select([
                'id',
                'uuid',
                'shop_id',
                'unit_id',
                'keywords',
                'img',
                'qr_code',
                'bar_code',
                'tax',
                'min_qty',
                'max_qty',
            ]),
            'countable.translation'      => fn($q) => $q->select('id', 'product_id', 'locale', 'title')
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language)
                ->orWhere('locale', $locale),
        ])
            ->find($products->whereNull('parent_id')->pluck('stock_id')->toArray());


        foreach ($stocks as $key => $stock) {

            if ($stock->countable?->shop_id !== (int)data_get($filter, 'shop_id')) {
                continue;
            }

            $product      = $products->where('stock_id', $stock->id)->first();
            $quantity     = (int)$this->actualQuantity($stock, data_get($product, 'quantity')) ?? 0;
            $price        = ($stock->price * $this->currency() * $quantity);
            $discount     = ($stock->actual_discount * $this->currency() * $quantity);
            $tax          = ($stock->tax_price * $this->currency() * $quantity);
            $totalPrice   = max(($price - $discount + $tax), 0);


            $result[$key] = [
                'id'                    => $stock->id,
                'price'                 => $price,
                'quantity'              => $quantity,
                'countable_quantity'    => $quantity,
                'tax'                   => $tax,
                'discount'              => $discount,
                'total_price'           => $totalPrice,
                'stock'                 => $stock->toArray(),
                'addons'                => []
            ];

            /** @var Bonus $bonus */
            $bonus = Bonus::with([
                'stock',
                'stock.countable' => fn($q) => $q->select([
                    'id',
                    'uuid',
                    'shop_id',
                    'unit_id',
                    'keywords',
                    'img',
                    'qr_code',
                    'bar_code',
                    'tax',
                    'min_qty',
                    'max_qty',
                ])->where('shop_id', data_get($filter, 'shop_id')),
                'stock.countable.translation' => fn($q) => $q->select('id', 'product_id', 'locale', 'title'),
                'stock.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
            ])->where([
                ['bonusable_type', Stock::class],
                ['bonusable_id', $stock->id],
                ['expired_at', '>', now()]
            ])->whereHas('stock.countable')->first();

            if ($quantity >= data_get($bonus, 'value', 0) &&
                $bonus?->stock?->countable?->shop_id === (int)data_get($filter, 'shop_id')
            ) {

                $bonusQuantity = (int)($bonus->type === Bonus::TYPE_COUNT ?
                        $bonus->bonus_quantity * floor($quantity / $bonus->value) :
                        $bonus->bonus_quantity);

                $bonusQuantity = $this->actualQuantity($bonus->stock, $bonusQuantity);

                if (empty($bonusQuantity)) {
                    continue;
                }

                $result[] = [
                    'id'          => $bonus->stock->id,
                    'price'       => 0,
                    'quantity'    => $bonusQuantity,
                    'tax'         => 0,
                    'discount'    => 0,
                    'total_price' => 0,
                    'bonus'       => true,
                    'stock'       => $bonus->stock->loadMissing([
                        'countable' => fn($q) => $q->select([
                            'id',
                            'uuid',
                            'shop_id',
                            'unit_id',
                            'keywords',
                            'img',
                            'qr_code',
                            'bar_code',
                            'tax',
                            'min_qty',
                            'max_qty',
                        ]),
                        'countable.translation'      => fn($q) => $q->select('id', 'product_id', 'locale', 'title')->where('locale', $this->language),
//                        'countable.shop.translation' => fn($q) => $q->select('id', 'shop_id', 'locale', 'title')->where('locale', $this->language),
                        'stockExtras',
                        'stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                    ]),
                ];
            }

            /** @var Stock[] $stockAddons */
            $stockAddons = Stock::with([
                'countable' => fn($q) => $q->select([
                    'id',
                    'uuid',
                    'shop_id',
                    'unit_id',
                    'keywords',
                    'img',
                    'qr_code',
                    'bar_code',
                    'tax',
                    'min_qty',
                    'max_qty',
                ]),
                'countable.translation'      => fn($q) => $q->select('id', 'product_id', 'locale', 'title')->where('locale', $this->language),
//                'countable.shop.translation' => fn($q) => $q->select('id', 'shop_id', 'locale', 'title')->where('locale', $this->language),
                'stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
            ])
                ->find($products->where('parent_id', $stock->id)->pluck('stock_id')->toArray());

            foreach ($stockAddons as $stockAddon) {

                $addedAddon = $products->where('stock_id', $stockAddon->id)->first();

                if (empty($addedAddon)) {
                    continue;
                }

                $addonQuantity   = (int)$this->actualQuantity($stockAddon, data_get($addedAddon, 'quantity')) ?? 0;
                $addonDiscount   = ($stockAddon->actual_discount  * $this->currency() * $addonQuantity);
                $addonPrice      = ($stockAddon->price  * $this->currency() * $addonQuantity);
                $addonTax        = ($stockAddon->tax_price  * $this->currency() * $addonQuantity);
                $addonTotalPrice = ($addonPrice - $addonDiscount + $addonTax);

                $result[$key]['price']          += $addonPrice;
                $result[$key]['quantity']       += $addonQuantity;
                $result[$key]['tax']            += $addonTax;
                $result[$key]['discount']       += $addonDiscount;
                $result[$key]['total_price']    += $addonTotalPrice;
                $result[$key]['addons'][]        = $stockAddon
                    ->setAttribute('price', $addonPrice)
                    ->setAttribute('quantity', $addonQuantity)
                    ->setAttribute('total_price', $addonTotalPrice)
                    ->setAttribute('discount', $addonDiscount)
                    ->setAttribute('tax', $addonTax)
                    ->toArray();

            }

        }

        $result = collect(array_values($result));

        $totalPrice = $result->sum('total_price');

        $shopTax     = 0;
        $km          = 0;
        $deliveryFee = 0;

        /** @var Shop $shop */
        $shop = Shop::with([
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])->find((int)data_get($filter, 'shop_id'));

        if (!empty($shop)) {

            $shopTax     = max((($totalPrice / $this->currency()) / 100 * $shop->tax) * $this->currency(), 0);

            $helper      = new Utility;
            $km          = $helper->getDistance($shop->location, data_get($filter, 'address', []));

            $deliveryFee = data_get($filter, 'type') === Order::DELIVERY ?
                $helper->getPriceByDistance($km, $shop, $this->currency()) : 0;

        }

        $coupon = Coupon::checkCoupon(data_get($filter, 'coupon'))->first();

        $couponPrice = 0;

        if ($coupon) {

            $couponPrice = $this->checkCoupon($coupon, $totalPrice);

            $totalPrice -= ($couponPrice * $this->currency());

        }

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => [
                'stocks'            => $result,
                'total_tax'         => $result->sum('tax'),
                'price'             => $result->sum('price'),
                'total_shop_tax'    => $shopTax,
                'total_price'       => max($totalPrice + $deliveryFee + $shopTax, 0),
                'total_discount'    => $result->sum('discount'),
                'delivery_fee'      => $deliveryFee,
                'rate'              => $this->currency(),
                'coupon_price'      => $couponPrice,
                'shop'              => $shop,
                'km'                => $km,
                'coupon'            => $coupon,
            ]
        ];
    }

    /**
     * @param int $id
     * @param int|null $shopId
     * @return Order|null
     */
    public function orderById(int $id, int $shopId = null): ?Order
    {
        return $this->model()
            ->withTrashed()
            ->with([
                'user' => fn($q) => $q->withCount(['orders' => fn($q) => $q->where('status', Order::STATUS_DELIVERED)])
                    ->withSum(['orders' => fn($q) => $q->where('status', Order::STATUS_DELIVERED)], 'total_price'),
                'review',
                'pointHistories',
                'currency' => fn($q) => $q->select('id', 'title', 'symbol'),
                'deliveryMan.deliveryManSetting',
                'coupon',
                'shop:id,location,tax,price,price_per_km,background_img,logo_img,uuid,phone',
                'shop.translation' => fn($q) => $q->where('locale', $this->language),
                'orderDetails' => fn($q) => $q->whereNull('parent_id'),
                'orderDetails.stock.countable.translation' => fn($q) => $q->where('locale', $this->language),
                'orderDetails.children.stock.countable.translation' => fn($q) => $q->where('locale', $this->language),
                'orderDetails.stock.stockExtras.group.translation' => function ($q) {
                    $q->select('id', 'extra_group_id', 'locale', 'title')->where('locale', $this->language);
                },
                'orderRefunds',
                'transaction.paymentSystem',
                'galleries',
            ])
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->find($id);
    }

    /**
     * @param Order|null $order
     * @return OrderResource|null
     */
    public function reDataOrder(?Order $order): OrderResource|null
    {
        return !empty($order) ? OrderResource::make($order) : null;
    }

    /**
     * @param Stock $stock
     * @param $quantity
     * @return int|mixed|null
     */
    private function actualQuantity(Stock $stock, $quantity): mixed
    {
        $countable = $stock->countable;

        if ($quantity < ($countable?->min_qty ?? 0)) {

            $quantity = $countable->min_qty;

        } else if($quantity > ($countable?->max_qty ?? 0)) {

            $quantity = $countable->max_qty;

        }

        return $quantity > $stock->quantity ? max($stock->quantity, 0) : $quantity;
    }

    /**
     * @param Coupon $coupon
     * @param $totalPrice
     * @return float|int|null
     */
    private function checkCoupon(Coupon $coupon, $totalPrice): float|int|null
    {
        if ($coupon->qty <= 0) {
            return 0;
        }

        return $coupon->type === 'percent' ? ($totalPrice / 100) * $coupon->price : $coupon->price;
    }

    public function ordersReportChart(array $filter): array
    {
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));

        $statistic = Order::where([
            ['created_at', '>=', $dateFrom],
            ['created_at', '<=', $dateTo],
            ['status', Order::STATUS_DELIVERED]
        ])
            ->select([
                DB::raw('sum(total_price) as total_price'),
                DB::raw('count(id) as count'),
            ])
            ->first();

        $quantity = OrderDetail::where([
            ['created_at', '>=', $dateFrom],
            ['created_at', '<=', $dateTo],
        ])
            ->whereHas('order',
                fn($q) => $q->select('id', 'status')
                    ->where('status', Order::STATUS_DELIVERED)
                    ->whereNull('deleted_at')
            )
            ->select([
                DB::raw('sum(quantity) as quantity')
            ])
            ->first();

        $type = data_get($filter, 'type');

        $keys = ['count', 'price', 'quantity'];
        $key  = in_array(data_get($filter, 'chart'), $keys) ? data_get($filter, 'chart') : 'count';

        $type = match ($type) {
            'year' => '%Y',
            'week' => '%w',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $select = match ($key) {
            'count' => 'count(id) as count',
            'price' => 'sum(total_price) as price',
            default => 'sum(quantity) as quantity',
        };

        if ($select === 'sum(quantity) as quantity') {
            $chart = OrderDetail::where([
                ['created_at', '>=', $dateFrom],
                ['created_at', '<=', $dateTo],
            ])
                ->whereHas('order',
                    fn($q) => $q->select('id', 'status')
                        ->where('status', Order::STATUS_DELIVERED)
                        ->whereNull('deleted_at')
                )
                ->select([
                    DB::raw("(DATE_FORMAT(created_at, '$type')) as time"),
                    DB::raw($select),
                ])
                ->groupBy('time')
                ->get();
        } else {
            $chart = Order::where([
                ['created_at', '>=', $dateFrom],
                ['created_at', '<=', $dateTo],
                ['status', Order::STATUS_DELIVERED]
            ])
                ->select([
                    DB::raw("(DATE_FORMAT(created_at, '$type')) as time"),
                    DB::raw($select),
                ])
                ->groupBy('time')
                ->get();
        }

        return [
            'chart'     => ChartRepository::chart($chart, $key),
            'currency'  => $this->currency,
            'count'     => data_get($statistic, 'count', 0),
            'price'     => data_get($statistic, 'total_price', 0),
            'quantity'  => $quantity?->quantity ?? 0,
        ];

    }

    /**
     * @param array $filter
     * @return array|Collection
     */
    public function ordersReportChartPaginate(array $filter): array|Collection
    {
        $dateFrom = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo   = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $default  = data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale');
        $key      = data_get($filter, 'column', 'id');
        $column   = data_get([
            'id',
            'total_price'
        ], $key, $key);

        $orders = Order::with(['user:id,firstname,lastname,active'])
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->where('status', Order::STATUS_DELIVERED)
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId));

        if (data_get($filter, 'export') === 'excel') {

            $name = 'orders-report-products-' . Str::random(8);

            try {
//                ExportJob::dispatch("export/$name.xlsx", $query->get(), OrdersReportExport::class);
                Excel::store(new OrdersReportExport($orders->get()), "export/$name.xlsx",'public');

                return [
                    'status'    => true,
                    'code'      => ResponseError::NO_ERROR,
                    'path'      => 'public/export',
                    'file_name' => "export/$name.xlsx",
                    'link'      => URL::to("storage/export/$name.xlsx"),
                ];
            } catch (Throwable $e) {
                $this->error($e);
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_501,
                    'message'   => $e->getMessage(),
                ];
            }
        }

        $orders = $orders->paginate(data_get($filter, 'perPage', 10));


        foreach ($orders as $i => $order) {

            $products = OrderDetail::with([
                'stock:id,countable_type,countable_id',
                'stock.countable:id',
                'stock.countable.translation' => fn($q) => $q
                    ->select('id', 'product_id', 'locale', 'title')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $default),
                'children:id,stock_id',
                'children.stock:id,countable_type,countable_id',
                'children.stock.countable:id',
                'children.stock.countable.translation' => fn($q) => $q
                    ->select('id', 'product_id', 'locale', 'title')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $default),
            ])
                ->where('order_id', $order->id)
                ->whereNull('parent_id')
                ->get();

            $result = [
                'id'            => $order->id,
                'status'        => $order->status,
                'firstname'     => $order->user?->firstname,
                'lastname'      => $order->user?->lastname,
                'active'        => $order->user?->active,
                'quantity'      => 0,
                'price'         => $order->total_price,
                'products'      => []
            ];

            foreach ($products as $product) {

                $children = collect($product?->children);

                $result['products'][] = $product->stock?->countable?->translation?->title . ' ' .
                    $children?->implode('stock.countable.translation.title', ', ');

                $result['quantity'] += (int)(($product?->quantity ?? 0) + ($children?->sum('quantity') ?? 0));
            }

            data_set($orders, $i, $result);
        }

        $isDesc = data_get($filter, 'sort', 'desc') === 'desc';

        return collect($orders)->sortBy($column, $isDesc ? SORT_DESC : SORT_ASC, $isDesc);
    }

    /**
     * @param array $filter
     * @return array
     */
    public function revenueReport(array $filter): array
    {
        $type       = data_get($filter, 'type');
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $column = [
            'count',
            'tax',
            'delivery_fee',
            'canceled_sum',
            'delivered_sum',
            'total_price',
            'total_quantity',
            'delivered_avg',
            'time',
        ];

        if (!in_array(data_get($filter, 'column'), $column)) {
            $filter['column'] = 'time';
        }

        $paginate = DB::table('orders as o')
            ->crossJoin('order_details as od', 'o.id', '=', 'od.order_id')
            ->where('o.created_at', '>=', $dateFrom)
            ->where('o.created_at', '<=', $dateTo)
            ->whereIn('o.status', [Order::STATUS_DELIVERED, Order::STATUS_CANCELED])
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('o.shop_id', $shopId))
            ->select([
                DB::raw("(DATE_FORMAT(o.created_at, " . ($type == 'year' ? "'%Y" : ($type == 'month' ? "'%Y-%m" : "'%Y-%m-%d")) . "')) as time"),
                DB::raw("sum(if(o.status = 'delivered', od.quantity, 0)) as total_delivered_quantity"),
                DB::raw("count(distinct o.id) as total_count"),
                DB::raw("sum(distinct o.total_price) as total_price"),
                DB::raw("sum(distinct if(o.status = 'delivered', o.delivery_fee, 0)) as total_delivery_fee"),
                DB::raw("count(distinct if(o.status = 'delivered', 1, 0)) as total_delivered_count"),
                DB::raw("sum(distinct if(o.status = 'canceled', o.total_price, 0)) as total_canceled_price"),
                DB::raw("sum(distinct if(o.status = 'delivered', o.total_price, 0)) as total_delivered_price"),
                DB::raw("sum(distinct if(o.status = 'delivered', o.tax, 0)) as total_delivered_tax"),
            ])
            ->groupBy('time')
            ->orderBy(data_get($filter, 'column', 'time'), data_get($filter, 'sort', 'desc'))
            ->get();

        if (data_get($filter, 'export') === 'excel') {

            $name = 'report-revenue-' . Str::random(8);

            try {
//                ExportJob::dispatch("export/$name.xlsx", $paginate, OrdersRevenueReportExport::class);
                Excel::store(new OrdersRevenueReportExport($paginate), "export/$name.xlsx",'public');

                return [
                    'status'    => true,
                    'code'      => ResponseError::NO_ERROR,
                    'path'      => 'public/export',
                    'file_name' => "export/$name.xlsx",
                    'link'      => URL::to("storage/export/$name.xlsx"),
                ];
            } catch (Throwable $e) {
                $this->error($e);
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_501,
                    'message'   => $e->getMessage(),
                ];
            }
        }

        return [
            'paginate'          => $paginate,
            'total_quantity'    => $paginate->sum('total_delivered_quantity'),
            'total_price'       => $paginate->sum('total_price'),
            'delivered_sum'     => $paginate->sum('total_delivered_price'),
            'delivered_count'   => $paginate->sum('total_delivered_count'),
            'total_tax'         => $paginate->sum('total_delivered_tax'),
            'total_count'       => $paginate->sum('total_count'),
        ];
    }

    /**
     * @param array $filter
     * @return array
     */
    public function overviewCarts(array $filter): array
    {
        $dateFrom = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo   = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $type = data_get($filter, 'type');
        $column = [
            'count',
            'tax',
            'delivery_fee',
            'canceled_sum',
            'delivered_sum',
            'delivered_avg',
            'time',
        ];

        if (!in_array(data_get($filter, 'column'), $column)) {
            $filter['column'] = 'time';
        }

        $chart = DB::table('orders')
            ->where('orders.created_at', '>=', $dateFrom)
            ->where('orders.created_at', '<=', $dateTo)
            ->whereIn('orders.status', [Order::STATUS_DELIVERED, Order::STATUS_CANCELED])
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('orders.shop_id', $shopId))
            ->selectRaw("
                sum(if(orders.status = 'delivered', 1, 0)) as count,
                sum(if(orders.status = 'delivered', orders.tax, 0)) as tax,
                sum(if(orders.status = 'delivered', orders.delivery_fee, 0)) as delivery_fee,
                sum(if(orders.status = 'canceled',  orders.total_price, 0)) as canceled_sum,
                sum(if(orders.status = 'delivered', orders.total_price, 0)) as delivered_sum,
                avg(if(orders.status = 'delivered', orders.total_price, 0)) as delivered_avg,
                (DATE_FORMAT(orders.created_at, " . ($type == 'year' ? "'%Y" : ($type == 'month' ? "'%Y-%m" : "'%Y-%m-%d")) . "')) as time
            ")
            ->groupBy('time')
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->get();

        return [
            'chart_price'   => ChartRepository::chart($chart, 'delivered_sum'),
            'chart_count'   => ChartRepository::chart($chart, 'count'),
            'count'         => $chart->sum('count'),
            'tax'           => $chart->sum('tax'),
            'delivery_fee'  => $chart->sum('delivery_fee'),
            'canceled_sum'  => $chart->sum('canceled_sum'),
            'delivered_sum' => $chart->sum('delivered_sum'),
            'delivered_avg' => $chart->sum('delivered_avg'),
        ];

    }

    /**
     * @param array $filter
     * @return array
     */
    public function overviewProducts(array $filter): array
    {
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $default    = data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale');
        $key        = data_get($filter, 'column', 'count');
        $shopId     = data_get($filter, 'shop_id');

        $column     = data_get([
            'id',
            'count',
            'total_price',
            'quantity',
        ], $key, $key);

        if ($column == 'id') {
            $column = 'p.id';
        }

        $orderDetails = DB::table('products as p')
            ->crossJoin('stocks as s', 'p.id', '=', 's.countable_id')
            ->crossJoin('order_details as od', 's.id', '=', 'od.stock_id')
            ->crossJoin('orders as o', function ($builder) use ($shopId, $dateFrom, $dateTo) {
                $builder->on('od.order_id', '=', 'o.id')
                    ->when($shopId, fn($q) => $q->where('o.shop_id', $shopId))
                    ->where('o.created_at', '>=', $dateFrom)
                    ->where('o.created_at', '<=', $dateTo)
                    ->whereIn('o.status', [Order::STATUS_DELIVERED, Order::STATUS_CANCELED]);
            })
            ->where('s.countable_type', '=', Product::class)
            ->select([
                DB::raw("sum(od.quantity) as quantity"),
                DB::raw("sum(od.total_price) as total_price"),
                DB::raw("count(od.id) as count"),
                DB::raw("p.id as id"),
            ])
            ->groupBy(['id'])
            ->having('count', '>', '0')
            ->orHaving('total_price', '>', '0')
            ->orHaving('quantity', '>', '0')
            ->orderBy($column, data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));

        $result = collect($orderDetails->items())->transform(function ($item) use ($default) {

            $translation = ProductTranslation::withTrashed()->where('product_id', data_get($item, 'id'))
                ->where(fn($q) => $q->where('locale', $this->language)->orWhere('locale', $default))
                ->select('title')
                ->first();

            $item->title = data_get($translation, 'title', 'EMPTY');

            return $item;
        });

        return [
            'data' => $result,
            'meta' => [
                'last_page'         => $orderDetails->lastPage(),
                'page'              => $orderDetails->currentPage(),
                'total'             => $orderDetails->total(),
                'more_pages'        => $orderDetails->hasMorePages(),
                'has_pages'         => $orderDetails->hasPages(),
            ]
        ];
    }

    /**
     * @param array $filter
     * @return array
     */
    public function overviewCategories(array $filter): array
    {
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $default    = data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale');
        $key        = data_get($filter, 'column', 'count');
        $shopId     = data_get($filter, 'shop_id');

        $column     = data_get([
            'id',
            'count',
            'total_price',
            'quantity',
        ], $key, $key);

        if ($column == 'id') {
            $column = 'c.id';
        }

        $orderDetails = DB::table('products as p')
            ->crossJoin('stocks as s', 'p.id', '=', 's.countable_id')
            ->crossJoin('order_details as od', 's.id', '=', 'od.stock_id')
            ->crossJoin('orders as o', function ($builder) use ($shopId, $dateFrom, $dateTo) {
                $builder->on('od.order_id', '=', 'o.id')
                    ->when($shopId, fn($q) => $q->where('o.shop_id', $shopId))
                    ->where('o.created_at', '>=', $dateFrom)
                    ->where('o.created_at', '<=', $dateTo)
                    ->whereIn('o.status', [Order::STATUS_DELIVERED, Order::STATUS_CANCELED]);
            })
            ->where('s.countable_type', '=', Product::class)
            ->select([
                DB::raw("sum(od.quantity) as quantity"),
                DB::raw("sum(od.total_price) as total_price"),
                DB::raw("count(od.id) as count"),
                DB::raw("p.category_id as id"),
            ])
            ->groupBy(['id'])
            ->having('count', '>', '0')
            ->orHaving('total_price', '>', '0')
            ->orHaving('quantity', '>', '0')
            ->orderBy($column, data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));

        $result = collect($orderDetails->items())->transform(function ($item) use ($default) {

            $translation = CategoryTranslation::withTrashed()->where('category_id', data_get($item, 'id'))
                ->where(fn($q) => $q->where('locale', $this->language)->orWhere('locale', $default))
                ->select('title')
                ->first();

            $item->title = data_get($translation, 'title', 'EMPTY');

            return $item;
        });

        return [
            'data' => $result,
            'meta' => [
                'last_page'         => $orderDetails->lastPage(),
                'page'              => $orderDetails->currentPage(),
                'total'             => $orderDetails->total(),
                'more_pages'        => $orderDetails->hasMorePages(),
                'has_pages'         => $orderDetails->hasPages(),
            ]
        ];
    }

}
