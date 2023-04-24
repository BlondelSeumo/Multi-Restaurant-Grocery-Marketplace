<?php

namespace App\Repositories\ShopClosedDateRepository;

use App\Models\Shop;
use App\Models\ShopClosedDate;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShopClosedDateRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ShopClosedDate::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        return Shop::with([
            'closedDates' => fn($q) => $q->select(['id', 'date', 'shop_id'])
        ])
            ->whereHas('closedDates')
            ->select('id', 'uuid', 'logo_img')
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param int $shopId
     * @return Collection
     */
    public function show(int $shopId): Collection
    {
        return ShopClosedDate::whereShopId($shopId)->select(['id', 'shop_id', 'date'])->get();
    }
}
