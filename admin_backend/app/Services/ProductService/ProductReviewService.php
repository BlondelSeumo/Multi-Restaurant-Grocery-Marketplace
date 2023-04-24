<?php

namespace App\Services\ProductService;

use App\Helpers\ResponseError;
use App\Models\Product;
use App\Services\CoreService;

class ProductReviewService extends CoreService
{

    protected function getModelClass(): string
    {
        return Product::class;
    }

    public function addReview($uuid, $collection): array
    {
        /** @var Product $product */

        $product = $this->model()->firstWhere('uuid', $uuid);

        if (empty(data_get($product, 'id'))) {
            return ['status' => false, 'code' => ResponseError::ERROR_404];
        }

        $product->addReview($collection);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $product
        ];
    }

    /**
     * @param $uuid
     * @return array
     */
    public function reviews($uuid): array
    {
        /** @var Product $product */

        $product = $this->model()->firstWhere('uuid', $uuid);

        if (empty(data_get($product, 'id'))) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $product->reviews
        ];
    }
}
