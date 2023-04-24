<?php

namespace App\Repositories\ProductRepository;

use App\Exports\ProductReportExport;
use App\Exports\StockExport;
use App\Exports\StockReportExport;
use App\Helpers\ResponseError;
use App\Http\Resources\ProductReportResource;
use App\Jobs\ExportJob;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Stock;
use App\Models\UserActivity;
use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\ProductRepoInterface;
use App\Repositories\ReportRepository\ChartRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Throwable;

class ProductRepository extends CoreRepository implements ProductRepoInterface
{
    protected function getModelClass(): string
    {
        return Product::class;
    }

    public function productsPaginate(array $filter): LengthAwarePaginator
    {
        /** @var Product $product */
        $product = $this->model();

        return $product
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->with([
                'shop' => fn($q) => $q->select('id', 'uuid', 'user_id', 'logo_img', 'background_img', 'type', 'status')
                    ->when(data_get($filter, 'shop_status'), function ($q, $status) {
                        $q->where('status', '=', $status);
                    }
                ),
                'shop.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'locale', 'title', 'shop_id'),
                'stocks.addons.addon' => fn($query) =>
                    $query
                        ->when(data_get($filter, 'addon_status'), fn($q, $status) => $q
                            ->where('active', true)
                            ->where('status', '=', $status)
                ),
                'stocks.addons.addon.stock',
                'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                'stocks' => fn($q) => $q->where('quantity', '>', 0),
                'stocks.bonus' => fn($q) => $q->where('expired_at', '>', now()),
                'stocks.bonus.stock',
                'stocks.bonus.stock.countable:id,uuid,tax,bar_code,status,active,img,min_qty,max_qty',
                'stocks.bonus.stock.countable.translation' => fn($q) => $q->select('id', 'product_id', 'title', 'locale'),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'discounts',
                'translation' => fn($q) => $q->where('locale', $this->language),
                'category' => fn($q) => $q->select('id', 'uuid'),
                'category.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'category_id', 'locale', 'title'),
                'brand' => fn($q) => $q->select('id', 'uuid', 'title'),
                'unit.translation' => fn($q) => $q->where('locale', $this->language),
                'tags.translation' => fn($q) => $q->select('id', 'category_id', 'locale', 'title')
                    ->where('locale', $this->language),
            ])
            ->withAvg('reviews', 'rating')
            ->whereHas('translation', fn($query) => $query->where('locale', $this->language))
            ->when(data_get($filter, 'shop_status'), function ($q, $status) {
                $q->whereHas('shop', function (Builder $query) use ($status) {
                    $query->where('status', '=', $status);
                });
            })
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function productDetails(int $id)
    {
        return $this->model()
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language))
            ->withAvg('reviews', 'rating')
            ->with([
                'stocks.addons',
                'stocks.addons.addon.stock',
                'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                'galleries' => fn($q) => $q->select('id', 'type', 'loadable_id', 'path', 'title'),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'discounts',
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
                'category.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'category_id', 'locale', 'title'),
                'brand' => fn($q) => $q->select('id', 'uuid', 'title'),
                'unit.translation' => fn($q) => $q->where('locale', $this->language),
                'extras.translation' => fn($q) => $q->where('locale', $this->language),
                'tags.translation' => fn($q) => $q->select('id', 'category_id', 'locale', 'title')
                    ->where('locale', $this->language),
        ])->find($id);
    }

    public function productByUUID(string $uuid): ?Product
    {
        /** @var Product $product */
        $product = $this->model();

        return $product
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with([
                'galleries' => fn($q) => $q->select('id', 'type', 'loadable_id', 'path', 'title'),
                'properties' => fn($q) => $q->where('locale', $this->language),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'stocks.addons.addon' => fn($q) => $q->where('active', true)
                    ->where('addon', true)
                    ->where('status', Product::PUBLISHED),
                'stocks.addons.addon.stock',
                'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                'discounts',
                'shop.translation' => fn($q) => $q->where('locale', $this->language),
                'category' => fn($q) => $q->select('id', 'uuid'),
                'category.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'category_id', 'locale', 'title'),
                'brand' => fn($q) => $q->select('id', 'uuid', 'title'),
                'unit.translation' => fn($q) => $q->where('locale', $this->language),
                'reviews.galleries',
                'reviews.user',
                'translation' => fn($q) => $q->where('locale', $this->language),
                'tags.translation' => fn($q) => $q->select('id', 'category_id', 'locale', 'title')
                    ->where('locale', $this->language),
            ])
            ->firstWhere('uuid', $uuid);
    }

    public function productsByIDs(array $filter = [])
    {
        return $this->model()
            ->filter($filter)
            ->with([
                'stocks.addons',
                'stocks.addons.addon.stock',
                'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'discounts',
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
                'tags.translation' => fn($q) => $q->select('id', 'category_id', 'locale', 'title')
                    ->where('locale', $this->language),
            ])
            ->whereHas('shop', function ($item) {
                $item->whereNull('deleted_at');
            })
            ->find(data_get($filter, 'products', []));
    }

    public function productsSearch(array $filter = []) {

        return $this->model()
            ->filter($filter)
            ->with([
                'stocks' => fn($q) => $q->select([
                    'id',
                    'countable_type',
                    'countable_id',
                    'price',
                    'quantity',
                ]),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'translation' => fn($q) => $q->select([
                    'id',
                    'product_id',
                    'locale',
                    'title',
                ])->where('locale', $this->language),
                'shop:id,status,uuid,user_id,logo_img,background_img,type',
            ])
            ->whereHas('shop', fn ($query) => $query->where('status', 'approved'))
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest()
            ->select([
                'id',
                'img',
                'shop_id',
                'uuid',
            ])
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function selectStockPaginate(array $data): LengthAwarePaginator
    {
        return Stock::with([
            'stockExtras.group.translation',
            'countable' => fn($q) => $q->select(['id', 'shop_id']),
            'countable.translation' => fn($q) => $q->select('id', 'product_id', 'locale', 'title')
                ->where('locale', $this->language),
        ])
            ->when(isset($array['addon']), fn($query, $addon) => $query->whereAddon($addon),
                fn($query) => $query->whereAddon(0)
            )
            ->whereHas('countable', fn($q) => $q->where('shop_id', data_get($data, 'shop_id') )
                ->when(data_get($data, 'status'), fn($q, $status) => $q->where('status', $status))
                ->when(data_get($data, 'search'), function ($q, $s) {

                    $q->where(function ($query) use ($s) {
                        $query->where('keywords', 'LIKE', "%$s%")
                            ->orWhereHas('translation', function ($q) use ($s) {
                                $q->where('title', 'LIKE', "%$s%")
                                    ->select('id', 'product_id', 'locale', 'title');
                            })
                            ->orWhereHas('shop', function ($q) use ($s) {
                                $q->where('take', 'like', "%$s%");
                            });
                    });

                })
            )
            ->whereHas('countable.translation', fn($q) => $q->where('locale', $this->language))
            ->select([
                'id',
                'countable_type',
                'countable_id',
            ])
            ->paginate(data_get($data, 'perPage', 10));
    }

    public function reportPaginate($products, $dateFrom, $dateTo, $column, $sort): array
    {
        $default    = data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale');
        $paginate   = [];

        foreach ($products as $product) {
            $findProduct = Product::withTrashed()
                ->with([
                    'translation' => fn($q) => $q->select('id', 'product_id', 'locale', 'title')
                        ->where('locale', $this->language)->orWhere('locale', $default),
                    'stocks:id,quantity,countable_type,countable_id',
                    'stocks.orderDetails:id,stock_id,total_price,quantity,order_id',
                    'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                    'category' => fn($q) => $q->select('id', 'uuid'),
                    'category.translation' => fn($q) => $q->where('locale', $this->language)
                        ->select('id', 'category_id', 'locale', 'title'),
                ])
                ->find($product->id);

            $stocks = $findProduct->stocks->transform(function (Stock $stock) use($dateFrom, $dateTo) {

                $orderDetails = $stock->orderDetails->load([
                    'order' => fn($q) => $q->select('id', 'status', 'created_at', 'deleted_at')
                        ->where('created_at', '>=', $dateFrom)
                        ->where('created_at', '<=', $dateTo)
                        ->where('status', Order::STATUS_DELIVERED)
                        ->whereNull('deleted_at'),
                ])->reject(fn(OrderDetail $orderDetail) => empty($orderDetail->order));

                return [
                    'id'        => $stock->id,
                    'extras'    => $stock->stockExtras,
                    'price'     => $orderDetails->sum('total_price'),
                    'quantity'  => $orderDetails->sum('quantity'),
                    'count'     => $orderDetails->groupBy('order_id')->count(),
                ];
            })->reject(fn(array $item) => data_get($item, 'count') === 0 &&
                data_get($item, 'quantity') === 0 && data_get($item, 'price') === 0
            )
                ->values()
                ->toArray();

            $paginate[$product->id] = [
                'id'            => $product->id,
                'uuid'          => $product->uuid,
                'active'        => $product->active,
                'category_id'   => $product->category_id,
                'shop_id'       => $product->shop_id,
                'keywords'      => $product->keywords,
                'bar_code'      => $product->bar_code,
                'count'         => $product->count,
                'quantity'      => $product->quantity,
                'price'         => $product->price,
                'stocks'        => $stocks,
                'translation'   => $findProduct->translation,
                'category'      => $findProduct->category,
            ];
        }

        $meta = [
            'last_page'         => $products->lastPage(),
            'page'              => $products->currentPage(),
            'total'             => $products->total(),
            'more_pages'        => $products->hasMorePages(),
            'has_pages'         => $products->hasPages(),
        ];

        $isDesc = $sort === 'desc';

        return [
            'data' => collect(array_values($paginate))
                ->sortBy($column, $isDesc ? SORT_DESC : SORT_ASC, $isDesc)
                ->values()
                ->toArray(),
            'meta' => $meta
        ];
    }

    public function reportChart(array $filter): array
    {
        $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
        $type       = data_get($filter, 'type');
        $chart      = data_get($filter, 'chart');

        $orders = Order::filter($filter)
            ->where([
                ['created_at', '>=', $dateFrom],
                ['created_at', '<=', $dateTo],
                ['status', '=', Order::STATUS_DELIVERED],
            ])
            ->select([
                DB::raw("(DATE_FORMAT(created_at, " . ($type == 'year' ? "'%Y" : ($type == 'month' ? "'%Y-%m" : "'%Y-%m-%d")) . "')) as time"),
                DB::raw('count(id) as count'),
                DB::raw('sum(total_price) as price'),
            ])
            ->withSum('orderDetails', 'quantity')
            ->groupBy(['time', 'order_details_sum_quantity'])
            ->get();

        $result = [];

        foreach ($orders as $order) {

            if (data_get($result, data_get($order, 'time'))) {
                $result[data_get($order, 'time')]['count'] += data_get($order, 'count', 0);
                $result[data_get($order, 'time')]['price'] += data_get($order, 'price', 0);
                $result[data_get($order, 'time')]['quantity'] += data_get($order, 'order_details_sum_quantity', 0);
                continue;
            }

            $result[data_get($order, 'time')] = [
                'time'      => data_get($order, 'time'),
                'count'     => data_get($order, 'count', 0),
                'price'     => data_get($order, 'price', 0),
                'quantity'  => data_get($order, 'order_details_sum_quantity', 0),
            ];

        }

        $result = collect(array_values($result));

        return [
            'chart'     => ChartRepository::chart($result, $chart),
            'count'     => $result->sum('count'),
            'price'     => $result->sum('price'),
            'quantity'  => $result->sum('quantity'),
        ];
    }

    public function productReportPaginate(array $filter): array
    {
        try {
            $dateFrom   = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
            $dateTo     = date('Y-m-d 23:59:59', strtotime(data_get($filter, 'date_to', now())));
            $default    = data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale');
            $key        = data_get($filter, 'column', 'id');
            $column     = data_get([
                'id',
                'bar_code',
                'category_id',
                'active',
                'shop_id',
                'deleted_at',
            ], $key, $key);

            $data = Product::withTrashed()->filter($filter)->with([

                'translation' => fn($q) => $q->withTrashed()
                    ->select('id', 'product_id', 'locale', 'title', 'deleted_at')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $default),

                'category' => fn($q) => $q->withTrashed()->select('id', 'deleted_at'),

                'category.translation'  => fn($q) => $q->withTrashed()->where('locale', $this->language)
                    ->orWhere('locale', $default)->select('id', 'category_id', 'locale', 'title', 'deleted_at'),

                'stocks' => fn($q) => $q->withTrashed()->select('id', 'countable_type', 'countable_id', 'quantity')
                    ->withSum('orderDetails', 'quantity'),

                'stocks.orderDetails' => fn($q) => $q->withTrashed()
                    ->select('id', 'order_id', 'stock_id', 'total_price', 'tax', 'quantity', 'deleted_at'),

                'stocks.orderDetails.order' => fn($q) => $q->withTrashed()
                    ->select('id', 'total_price', 'tax', 'shop_id', 'status', 'created_at', 'deleted_at')
                    ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
                    ->where('created_at', '>=', $dateFrom)
                    ->where('created_at', '<=', $dateTo)
                    ->where('status', Order::STATUS_DELIVERED),

                'stocks.stockExtras' => fn($q) => $q->withTrashed(),
                'stocks.stockExtras.group' => fn($q) => $q->withTrashed()->select('id', 'deleted_at'),
                'stocks.stockExtras.group.translation' => fn($q) => $q->withTrashed()->where('locale', $this->language)
                    ->orWhere('locale', $default),

            ])
            ->has('stocks.orderDetails.order')
            ->select([
                'id',
                'bar_code',
                'category_id',
                'active',
                'shop_id',
                'deleted_at',
            ])
            ->orderBy($column, data_get($filter, 'sort', 'desc'));

            if (data_get($filter, 'export') === 'excel') {

                $name = 'products-report-' . Str::random(8);

                $result = ProductReportResource::collection($data->get())->toArray(request());
                Excel::store(new ProductReportExport($result), "export/$name.xlsx",'public');

                return [
                    'status' => true,
                    'code'   => ResponseError::NO_ERROR,
                    'data'   => [
                        'path'      => 'public/export',
                        'file_name' => "export/$name.xlsx",
                        'link'      => URL::to("storage/export/$name.xlsx"),
                    ]
                ];

            }

            $data = $data->paginate(data_get($filter, 'perPage', 10));

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => [
                    'data'   => ProductReportResource::collection($data),
                    'meta'   => [
                        'last_page'     => $data->lastPage(),
                        'page'          => $data->currentPage(),
                        'total'         => $data->total(),
                        'more_pages'    => $data->hasMorePages(),
                        'has_pages'     => $data->hasPages(),
                    ]
                ],
            ];

        } catch (Throwable $e) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_400,
                'message'   => $e->getMessage()
            ];
        }
    }

    /**
     * @param array $filter
     * @return array|LengthAwarePaginator
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function stockReportPaginate(array $filter): LengthAwarePaginator|array
    {
        $query = Product::filter($filter)
            ->with([
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
                'stocks:id,countable_type,countable_id,quantity',
            ])
            ->when(is_array(data_get($filter, 'products')), function (Builder $query) use($filter) {
                $query->whereIn('id', data_get($filter, 'products'));
            })
            ->when(is_array(data_get($filter, 'categories')), function (Builder $query) use($filter) {
                $query->whereIn('category_id', data_get($filter, 'categories'));
            })
            ->select([
                'id',
                'bar_code',
                'status',
                'shop_id',
                'keywords'
            ])
            ->withSum('stocks', 'quantity')
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'));

        if (data_get($filter, 'export') === 'excel') {

            $name = 'stocks-report-' . Str::random(8);

            Excel::store(new StockReportExport($query->get()), "export/$name.xlsx",'public');
//            ExportJob::dispatchAfterResponse($name, $query->get(), StockReportExport::class);

            return [
                'path'      => 'public/export',
                'file_name' => "export/$name.xlsx",
                'link'      => URL::to("storage/export/$name.xlsx"),
            ];
        }

        return $query->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @return array|LengthAwarePaginator
     */
    public function extrasReportPaginate(array $filter): LengthAwarePaginator|array
    {

        if (data_get($filter, 'export') === 'excel') {

            $name = 'stocks-report-' . Str::random(8);

            ExportJob::dispatch("export/$name.xlsx", collect([]), StockExport::class);

            return [
                'path'      => 'public/export',
                'file_name' => "export/$name.xlsx",
                'link'      => URL::to("storage/export/$name.xlsx"),
            ];
        }

        $query = Stock::with([
                'countable:id,uuid,active,category_id,bar_code,shop_id',
                'countable.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
                'stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'orderDetails:stock_id,order_id,id,quantity',
                'orderDetails.order:id,total_price',
            ])
            ->whereHas('countable', function (Builder $query) use ($filter) {

                if (data_get($filter, 'products')) {
                    $query->whereIn('id', data_get($filter, 'products'));
                }

                if (data_get($filter, 'categories')) {
                    $query->whereIn('category_id', data_get($filter, 'categories'));
                }

                if (!empty(data_get($filter, 'shop_id')) && is_int(data_get($filter, 'shop_id'))) {
                    $query->where('shop_id', data_get($filter, 'shop_id'));
                }

            })
            ->whereHas('stockExtras')
            ->select([
                'id',
                'countable_type',
                'countable_id',
                'price',
                'quantity',
            ])
            ->whereHas('orderDetails', fn($q) => $q->withSum('order', 'total_price'))
            ->withSum('orderDetails', 'quantity')
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'));

        return $query->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function history(array $filter): LengthAwarePaginator
    {
        $agent = new Agent;

        $where = [
            'model_type' => Product::class,
            'device'     => $agent->device(),
            'ip'         => request()->ip(),
        ];

        if (data_get($filter, 'user_id')) {

            $where = [
                'model_type' => Product::class,
                'device'     => $agent->device(),
                'user_id'    => data_get($filter, 'user_id'),
            ];

        }

        return UserActivity::where($where)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function mostPopulars(array $filter): LengthAwarePaginator
    {
        $type       = data_get($filter, 'type');
        $locale     = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        $filter['model_type'] = Product::class;

        if (data_get($filter, 'date_from')) {
            $filter['date_from']  = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_from')));
        }

        if (data_get($filter, 'date_to')) {
            $filter['date_to']  = date('Y-m-d 00:00:01', strtotime(data_get($filter, 'date_to')));
        }

        return UserActivity::filter($filter)
            ->with([
                'model.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
            ])
            ->select([
                'model_type',
                'model_id',
                DB::raw('count(model_id) as count'),
            ])
            ->groupBy('model_id', 'model_type')
            ->orderBy('count', 'desc')
            ->paginate(data_get($filter, 'perPage', 10));
    }

}

