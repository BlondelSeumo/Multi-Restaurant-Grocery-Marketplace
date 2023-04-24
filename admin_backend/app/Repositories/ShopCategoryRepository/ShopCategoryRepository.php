<?php

namespace App\Repositories\ShopCategoryRepository;


use App\Models\ShopCategory;
use App\Repositories\CoreRepository;

class ShopCategoryRepository extends CoreRepository
{

    protected function getModelClass(): string
    {
        return ShopCategory::class;
    }

    public function getById(int $categoryId, int $shopId)
    {
        return $this->model()->where('category_id', $categoryId)->where('shop_id', $shopId)->first();
    }
}
