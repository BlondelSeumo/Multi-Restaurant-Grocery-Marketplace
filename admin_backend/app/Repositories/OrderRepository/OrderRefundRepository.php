<?php

namespace App\Repositories\OrderRepository;

use App\Models\OrderRefund;
use App\Models\User;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderRefundRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return OrderRefund::class;
    }

    /**
     * @param array $filter
     * @return array|Collection
     */
    public function list(array $filter = []): array|Collection
    {
        /** @var OrderRefund $orderRefund */

        $orderRefund = $this->model();

        return $orderRefund
            ->filter($filter)
            ->with([
                'order' => fn($q) => $q->select('id', 'shop_id', 'user_id', 'status', 'created_at'),
                'order.shop:id,uuid',
                'order.shop.translation' => fn($q) => $q->where('locale', $this->language)
                    ->select('id', 'locale', 'title', 'shop_id')
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->get();
    }

    public function paginate(array $filter = []): LengthAwarePaginator
    {
        /** @var OrderRefund $orderRefund */

        $orderRefund = $this->model();

        if (data_get($filter, 'user_uuid')) {

            $user = User::whereUuid(data_get($filter, 'user_uuid'))->select(['uuid', 'id'])->first();

            $filter['user_id'] = $user?->id;

        }

        return $orderRefund
            ->filter($filter)
            ->with([
                'order' => fn($q) => $q
                    ->when(data_get($filter, 'shop_id') || data_get($filter, 'user_id'), function ($q) use ($filter) {
                        if (data_get($filter, 'shop_id')) {
                            $q->where('shop_id', data_get($filter, 'shop_id'));
                        }
                        if (data_get($filter, 'user_id')) {
                            $q->where('user_id', data_get($filter, 'user_id'));
                        }
                    })
                    ->select('id', 'shop_id', 'user_id', 'status', 'total_price', 'created_at'),
                'order.shop:id,uuid,logo_img',
                'order.shop.translation' => fn($q) => $q->where('locale', $this->language)
                                                        ->select('id', 'locale', 'title', 'shop_id'),
                'order.user:id,firstname,lastname,uuid'
            ])
            ->whereHas('order', fn($q) => $q
                ->when(data_get($filter, 'shop_id') || data_get($filter, 'user_id'), function ($q) use ($filter) {
                    if (data_get($filter, 'shop_id')) {
                        $q->where('shop_id', data_get($filter, 'shop_id'));
                    }
                    if (data_get($filter, 'user_id')) {
                        $q->where('user_id', data_get($filter, 'user_id'));
                    }
                }))
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function show(OrderRefund $orderRefund): OrderRefund
    {
        return $orderRefund->load([
            'order',
            'order.shop',
            'order.shop.translation' => fn($q) => $q->where('locale', $this->language)
                ->select('id', 'locale', 'title', 'shop_id'),
            'order.user:id,firstname,lastname,uuid',
            'order.deliveryMan.deliveryManSetting',
            'order.orderDetails.stock.stockExtras.group.translation' => function ($q) {
                $q->select('id', 'extra_group_id', 'locale', 'title')->where('locale', $this->language);
            },
            'order.orderDetails.stock.countable.translation' => function ($q) {
                $q->select('id', 'product_id', 'locale', 'title')->where('locale', $this->language);
            },
        ]);
    }
}
