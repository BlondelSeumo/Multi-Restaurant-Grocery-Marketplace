<?php

namespace App\Repositories\ProductRepository;

use App\Models\Language;
use App\Models\Product;
use App\Repositories\CoreRepository;
use Cache;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class RestProductRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Product::class;
    }

    public function productsPaginate(array $filter): LengthAwarePaginator
    {
        /** @var Product $product */
        $product = $this->model();
        $locale  = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $product
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->with([
                'stocks' => fn($q) => $q->where('addon', false)->where('quantity', '>', 0),
                'stocks.addons.addon' => fn($query) => $query->when(data_get($filter, 'addon_status'),
                    fn($q, $status) => $q->with([
                        'stock'         => fn($q) => $q->where('addon', false)->where('quantity', '>', 0),
                        'translation'   => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
                    ])
                        ->whereHas('stock', fn($q) => $q->where('addon', false)->where('quantity', '>', 0))
                        ->whereHas('translation', fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale))
                        ->where('active', true)
                        ->where('status', '=', $status)
                ),
                'stocks.bonus' => fn($q) => $q->where('expired_at', '>', now())->select([
                    'id', 'expired_at', 'bonusable_type', 'bonusable_id',
                    'bonus_quantity', 'value', 'type', 'status'
                ]),
                'stocks.bonus.stock',
                'stocks.bonus.stock.countable:id,uuid,tax,bar_code,status,active,img,min_qty,max_qty',
                'stocks.bonus.stock.countable.translation' => fn($q) => $q->select('id', 'product_id', 'title', 'locale'),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
                'discounts',
                'translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            ])
            ->whereHas('translation', fn($query) => $query->where('locale', $this->language)->orWhere('locale', $locale))
            ->when(data_get($filter, 'shop_status'), function ($q, $status) {
                $q->whereHas('shop', function (Builder $query) use ($status) {
                    $query->where('status', '=', $status);
                });
            })
            ->when(data_get($filter, 'rating'), function (Builder $q, $rating) {

                $rtg = [
                    0 => data_get($rating, 0, 0),
                    1 => data_get($rating, 1, 5),
                ];

                $q->withAvg([
                    'reviews' => fn(Builder $b) => $b->whereBetween('rating', $rtg)
                ], 'rating')->whereHas('reviews',
                    fn(Builder $b) => $b->whereBetween('rating', $rtg)
                );

            }, fn($q) => $q->withAvg('reviews', 'rating'))
            ->when(data_get($filter, 'order_by'), function (Builder $query, $orderBy) {

                switch ($orderBy) {
                    case 'new':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'old':
                        $query->orderBy('created_at');
                        break;
                    case 'best_sale':
                        $query->withCount('orders')->orderBy('orders_count', 'desc');
                        break;
                    case 'low_sale':
                        $query->withCount('orders')->orderBy('orders_count');
                        break;
                    case 'high_rating':
                        $query->orderBy('reviews_avg_rating', 'desc');
                        break;
                    case 'low_rating':
                        $query->orderBy('reviews_avg_rating');
                        break;
                    case 'trust_you':
                        $ids = implode(', ', array_keys(Cache::get('shop-recommended-ids', [])));
                        $query->orderByRaw(DB::raw("FIELD(id, $ids) ASC"));
                        break;
                }

            }, fn($q) => $q->orderBy(
                data_get($filter, 'column', 'id'),
                data_get($filter, 'sort', 'desc')
            ))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function productsMostSold(array $filter = [])
    {
        return $this->model()
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->whereHas('translation', function ($q) {
                $q->where('locale', $this->language);
            })
            ->withAvg('reviews', 'rating')
            ->withCount('orderDetails')
            ->with([
                'stocks' => fn($q) => $q->where('quantity', '>', 0),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
                ])
            ->whereHas('stocks', function ($item){
                $item->where('quantity', '>', 0);
            })
            ->whereHas('shop', function ($item) {
                $item->whereNull('deleted_at');
            })
            ->whereActive(1)
            ->orderBy('order_details_count', 'desc')
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @return mixed
     */
    public function productsDiscount(array $filter = []): mixed
    {
        $profitable = data_get($filter, 'profitable') ? '=' : '>=';

        return $this->model()
            ->with([
                'discounts',
                'stocks' => fn($q) => $q->where('quantity', '>', 0),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language),
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'product_id', 'locale', 'title'),
            ])
            ->withAvg('reviews', 'rating')
            ->whereActive(1)
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->whereHas('discounts', function ($item) use ($profitable) {
                $item->where('active', 1)
                    ->whereDate('start', '<=', today())
                    ->whereDate('end', $profitable, today()->format('Y-m-d'));
            })
            ->whereHas('translation', function ($q) {
                $q->where('locale', $this->language);
            })
            ->whereHas('stocks', function ($item){
                $item->where('quantity', '>', 0);
            })
            ->whereHas('shop')
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param string $uuid
     * @return Product|null
     */
    public function productByUUID(string $uuid): ?Product
    {
        /** @var Product $product */
        $product = $this->model();
        $locale  = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $product
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language))
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with([
                'stocks' => fn($q) => $q->with([
                    'bonus' => fn($q) => $q->where('expired_at', '>', now())->select([
                        'id', 'expired_at', 'bonusable_type', 'bonusable_id',
                        'bonus_quantity', 'value', 'type', 'status'
                    ]),
                ]),
                'stocks.stockExtras.group.translation' => fn($q) => $q->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'stocks.addons' => fn($q) => $q->whereHas('addon', fn($a) => $a->whereHas('stock')),
                'stocks.addons.addon' => fn($q) => $q
                    ->with([
                        'stock',
                        'translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
                    ])
                    ->whereHas('stock')
                    ->select([
                        'id',
                        'uuid',
                        'category_id',
                        'unit_id',
                        'img',
                        'active',
                        'status',
                        'min_qty',
                        'max_qty',
                    ])
                    ->where('active', true)
                    ->where('addon', true)
                    ->where('status', Product::PUBLISHED),
                'discounts',
                'translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            ])
            ->where('active', true)
            ->where('addon', false)
            ->where('status', Product::PUBLISHED)
            ->firstWhere('uuid', $uuid);
    }
}
