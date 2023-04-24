<?php

namespace App\Repositories\BannerRepository;

use App\Models\Banner;
use App\Repositories\CoreRepository;

class BannerRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Banner::class;
    }

    public function bannersPaginate(array $filter)
    {
        return $this->model()
            ->withCount('likes')
            ->with([
                'translation' => fn($q) => $q->where('locale', $this->language)
            ])
            ->when(data_get($filter, 'active'), function ($q, $active) {
                $q->where('active', $active);
            })
            ->when(data_get($filter, 'type'), function ($q, $type) {
                $q->where('type', $type);
            })
            ->when(data_get($filter, 'shop_ids'), function ($q, $shopIds) {
                $q->whereHas('shops', fn($q) => $q->whereIn('id', $shopIds));
            })
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->select([
                'id',
                'url',
                'type',
                'img',
                'active',
                'created_at',
                'updated_at',
                'deleted_at',
                'clickable',
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function bannerDetails(int $id)
    {
        return $this->model()
            ->withCount('likes')
            ->withCount('shops')
            ->with([
                'galleries',
                'shops:id,uuid,user_id,status,logo_img,open,delivery_time',
                'shops' => fn($q) => $q->withAvg('reviews', 'rating')->withCount('reviews')->paginate(request('perPage', 2)),
                'shops.translation' => fn($q) => $q->where('locale', $this->language),
                'translation' => fn($q) => $q->where('locale', $this->language),
                'translations',
            ])
            ->find($id);
    }
}
