<?php

namespace App\Repositories\CouponRepository;

use App\Models\Coupon;
use App\Repositories\CoreRepository;

class CouponRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Coupon::class;
    }

    public function couponsList(array $filter)
    {
        return $this->model()->with([
            'translation' => fn($q) => $q->where('locale', $this->language)
                ->select('id', 'coupon_id', 'locale', 'title')
        ])
            ->filter($filter)
            ->get();
    }

    public function couponsPaginate(array $filter)
    {
        return $this->model()->whereHas('translation', function ($q) {
            $q->where('locale', $this->language);
        })
            ->with([
                'translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'coupon_id', 'locale', 'title')
            ])
            ->when(data_get($filter, 'shop_id'), function ($q, $shopId) {
                $q->where('shop_id', $shopId);
            })
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function couponByName(string $name)
    {
        return $this->model()->with([
            'galleries',
            'translation' => fn($q) => $q->where('locale', $this->language)
                ->select('id', 'coupon_id', 'locale', 'title')
        ])->firstWhere('name', $name);
    }

    public function couponById(int $id, $shop = null)
    {
        return $this->model()->with([
            'shop',
            'galleries',
            'translation' => fn($q) => $q->where('locale', $this->language)
                ->select('id', 'coupon_id', 'locale', 'title')
        ])
            ->when(isset($shop), function ($q) use($shop) {
                $q->where('shop_id', $shop);
            })->find($id);
    }
}
