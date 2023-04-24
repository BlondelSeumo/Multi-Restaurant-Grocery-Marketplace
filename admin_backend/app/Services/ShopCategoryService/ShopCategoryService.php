<?php

namespace App\Services\ShopCategoryService;

use App\Helpers\ResponseError;
use App\Models\ShopCategory;
use App\Services\CoreService;
use Throwable;

class ShopCategoryService extends CoreService
{

    protected function getModelClass(): string
    {
        return ShopCategory::class;
    }

    public function create(array $data): array
    {
        foreach (data_get($data, 'categories', []) as $category_id) {
            $this->model()->firstOrCreate([
                'shop_id'       => data_get($data, 'shop_id'),
                'category_id'   => $category_id
            ]);
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => []];
    }

    public function update($collection, $shop): array
    {
        $shopCategory = $shop->categories();

        $shopCategory->sync(data_get($collection, 'categories', []));

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => []];

    }

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        try {
            $shopCategories = $this->model()
                ->whereIn('category_id', is_array($ids) ? $ids : [])
                ->where('shop_id', $shopId)
                ->get();

            foreach ($shopCategories as $shopCategory) {
                $shopCategory->delete();
            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Throwable) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_505,
                'message' => __('errors.' . ResponseError::ERROR_505, locale: $this->language)
            ];
        }
    }

}
