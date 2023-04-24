<?php

namespace App\Repositories\ShopWorkingDayRepository;

use App\Models\Shop;
use App\Models\ShopWorkingDay;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShopWorkingDayRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ShopWorkingDay::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        return Shop::with([
            'workingDays' => fn($q) => $q->select(['id', 'day', 'from', 'to', 'disabled', 'shop_id'])
        ])
            ->whereHas('workingDays')
            ->select('id', 'uuid', 'logo_img')
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param int $shopId
     * @return Collection
     */
    public function show(int $shopId): Collection
    {
        return ShopWorkingDay::select(['id', 'day', 'to', 'from', 'disabled'])
            ->where('shop_id', $shopId)
            ->orderBy('day')
            ->get();
    }
}
