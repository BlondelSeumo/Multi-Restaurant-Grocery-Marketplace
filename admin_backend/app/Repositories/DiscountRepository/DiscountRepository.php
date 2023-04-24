<?php

namespace App\Repositories\DiscountRepository;

use App\Models\Discount;
use App\Repositories\CoreRepository;

class DiscountRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Discount::class;
    }

    public function discountsPaginate(array $filter = [])
    {
        return $this->model()
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort','desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function discountDetails(Discount $discount): Discount
    {
        return $discount->load([
            'galleries',
            'products.translation' => function($q) {
                $q->select('id', 'product_id', 'locale', 'title')->where('locale', $this->language);
            }]);
    }

}
