<?php

namespace App\Repositories\ShopRepository;

use App\Models\Language;
use App\Models\Shop;
use App\Repositories\CoreRepository;
use Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminShopRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Shop::class;
    }

    /**
     * Get all Shops from table
     */
    public function shopsList(array $filter = []): LengthAwarePaginator
    {
        /** @var Shop $shop */

        $shop = $this->model();

        return $shop
            ->updatedDate($this->updatedDate)
            ->filter($filter)
            ->with([
                'translation'   => fn($q) => $q->where('locale', $this->language),
                'seller'        => fn($q) => $q->select('id', 'firstname', 'lastname', 'uuid'),
                'seller.roles',
            ])
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language))
            ->orderByDesc('id')
            ->select([
                'id',
                'background_img',
                'logo_img',
                'open',
                'tax',
                'status',
                'type',
                'deleted_at',
            ])
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * Get one Shop by UUID
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function shopsPaginate(array $filter): LengthAwarePaginator
    {
        /** @var Shop $shop */
        $shop = $this->model();

        return $shop
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->with([
                'translation' => function ($query) use ($filter) {

                    $query->when(data_get($filter, 'not_lang'),
                        fn($q, $notLang) => $q->where('locale', '!=', data_get($filter, 'not_lang')),
                        fn($q) => $q->where('locale', '=', $this->language),
                    );

                },
                'translations:id,locale,shop_id',
                'seller:id,firstname,lastname,uuid,active',
            ])
            ->whereHas('translation', function ($query) use ($filter) {

                $query->when(data_get($filter, 'not_lang'),
                    fn($q, $notLang) => $q->where('locale', '!=', data_get($filter, 'not_lang')),
                    fn($q) => $q->where('locale', '=', $this->language),
                );

            })
            ->select([
                'id',
                'uuid',
                'background_img',
                'logo_img',
                'open',
                'tax',
                'status',
                'type',
                'user_id',
                'deleted_at',
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param string $uuid
     * @return Model|Builder|null
     */
    public function shopDetails(string $uuid): Model|Builder|null
    {
        /** @var Shop $shop */
        $shop = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        return $shop->with([
            'translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'subscription',
            'seller:id,firstname,lastname,uuid',
            'seller.roles',
            'workingDays',
            'closedDates',
            'categories:id',
            'categories.translation' => fn($q) => $q->select('category_id', 'id', 'locale', 'title')
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'bonus' => fn($q) => $q->where('expired_at', '>=', now())
                ->select([
                    'bonusable_type',
                    'bonusable_id',
                    'bonus_quantity',
                    'bonus_stock_id',
                    'expired_at',
                    'value',
                    'type',
                ]),
            'bonus.stock.countable' => fn($q) => $q->select('id', 'uuid'),
            'bonus.stock.countable.translation' => fn($q) => $q->where('locale', $this->language)
                ->orWhere('locale', $locale)
                ->select('id', 'locale', 'title', 'product_id'),
            'discounts' => fn($q) => $q->where('end', '>=', now())
                ->select('id', 'shop_id', 'type', 'end', 'price', 'active', 'start'),
            'shopPayments:id,payment_id,shop_id,status,client_id,secret_id',
            'shopPayments.payment:id,tag,input,sandbox,active',
            'tags:id,img',
            'tags.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
        ])
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale))
            ->where(fn($q) => $q->where('uuid', $uuid)->orWhere('id', $uuid))
            ->first();
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function shopsSearch(array $filter): LengthAwarePaginator
    {
        /** @var Shop $shop */
        $shop = $this->model();

        return $shop
            ->filter($filter)
            ->with([
                'translation'   => fn($q) => $q->where('locale', $this->language),
                'discounts'     => fn($q) => $q->where('end', '>=', now())->select('id', 'shop_id', 'end'),
            ])
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language))
            ->latest()
            ->select([
                'id',
                'logo_img',
                'status',
            ])
            ->paginate(data_get($filter, 'perPage', 10));
    }

}
