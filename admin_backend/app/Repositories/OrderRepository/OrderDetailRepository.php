<?php

namespace App\Repositories\OrderRepository;

use App\Models\OrderDetail;
use App\Repositories\CoreRepository;

class OrderDetailRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrderDetail::class;
    }

    public function paginate(array $filter = [])
    {
        return $this->model()
            ->with([
                'orderDetails.stock',
                'order.currency' => function ($q) {
                    $q->select('id', 'title', 'symbol');
                },
                'orderDetails.stock.countable.translation' => function ($q) {
                    $q->select('id', 'product_id', 'locale', 'title')
                        ->where('locale', $this->language);
                }
            ])
            ->updatedDate($this->updatedDate)
            ->when(data_get($filter, 'user_id'), function ($q, $userId) {
                $q->whereHas('order', function ($query) use($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function orderDetailById(int $id)
    {
        return $this->model()->find($id);
    }

}
