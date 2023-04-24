<?php

namespace App\Repositories\ShopTagRepository;

use App\Models\ShopTag;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ShopTagRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ShopTag::class;
    }

    public function paginate($data = []): LengthAwarePaginator
    {
        /** @var ShopTag $shopTags */
        $shopTags = $this->model();

        return $shopTags
            ->with([
                'translation'       => fn($q) => $q->where('locale', $this->language),
            ])
            ->when(data_get($data, 'search'), function (Builder $query, $search) {
                $query->whereHas('translation', fn($q) => $q->where('title', 'like', "%$search%"));
            })
            ->when(isset($data['deleted_at']), fn($q) => $q->onlyTrashed())
            ->orderBy(data_get($data, 'column', 'id'), data_get($data, 'sort', 'desc'))
            ->paginate(data_get($data, 'perPage', 10));
    }

    public function show(ShopTag $shopTag): ShopTag
    {
        return $shopTag->loadMissing([
            'translations',
            'translation'       => fn($q) => $q->where('locale', $this->language),
        ]);
    }
}
