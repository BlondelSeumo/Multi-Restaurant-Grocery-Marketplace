<?php

namespace App\Services\ProductService;

use App\Helpers\ResponseError;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Services\CoreService;
use App\Services\Interfaces\ProductServiceInterface;
use App\Traits\SetTranslations;
use Throwable;

class ProductService extends CoreService implements ProductServiceInterface
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {

            if (
                !empty(data_get($data, 'category_id')) &&
                $this->checkIsParentCategory(data_get($data, 'category_id'))
            ) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_501,
                    'message' => __('errors.' . ResponseError::CATEGORY_IS_PARENT, locale: $this->language)
                ];
            }

            if (data_get($data, 'addon') && data_get($data, 'addons.*')) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_501,
                    'message' => __('errors.' . ResponseError::ATTACH_FOR_ADDON, locale: $this->language)
                ];
            }

            /** @var Product $product */
            if (data_get($data, 'min_qty') > 1000000) {
                data_set($data, 'min_qty',1000000);
            }

            if (data_get($data, 'max_qty') > 1000000) {
                data_set($data, 'max_qty',1000000);
            }

            $product = $this->model()->create($data);

            $this->setTranslations($product, $data);

            if (data_get($data, 'meta')) {
                $product->setMetaTags($data);
            }

            if (data_get($data, 'images.0')) {
                $product->update(['img' => data_get($data, 'images.0')]);
                $product->uploads(data_get($data, 'images'));
            }

//            if (!Cache::has(base64_decode('cHJvamVjdC5zdGF0dXM=')) || Cache::get(base64_decode('cHJvamVjdC5zdGF0dXM='))->active != 1) {
//                return ['status' => false, 'code' => ResponseError::ERROR_403];
//            }

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $product->loadMissing([
                    'translations',
                    'metaTags',
                    'stocks.addons',
                    'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                ])
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return array
     */
    public function update(string $uuid, array $data): array
    {
        try {

            if (
                !empty(data_get($data, 'category_id')) &&
                $this->checkIsParentCategory(data_get($data, 'category_id'))
            ) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_501,
                    'message' => __('errors.' . ResponseError::CATEGORY_IS_PARENT, locale: $this->language)
                ];
            }

            if (data_get($data, 'addon') && data_get($data, 'addons.*')) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_501,
                    'message' => __('errors.' . ResponseError::ATTACH_FOR_ADDON, locale: $this->language)
                ];
            }

            /** @var Product $product */
            $product = $this->model()->firstWhere('uuid', $uuid);

            if (empty($product)) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_404,
                    'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
                ];
            }

            if (data_get($data, 'min_qty') &&
                data_get($data, 'max_qty') &&
                data_get($data, 'min_qty') > data_get($data, 'max_qty')
            ) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_400,
                    'message'   => 'max qty must be more than min qty'
                ];
            }

            if (data_get($data, 'min_qty') > 1000000) {
                data_set($data, 'min_qty',1000000);
            }

            if (data_get($data, 'max_qty') > 1000000) {
                data_set($data, 'max_qty',1000000);
            }

            $product->update($data);

            $this->setTranslations($product, $data);

            if (data_get($data, 'meta')) {
                $product->setMetaTags($data);
            }

            if (data_get($data, 'images.0')) {
                $product->galleries()->delete();
                $product->update([ 'img' => data_get($data, 'images.0') ]);
                $product->uploads(data_get($data, 'images'));
            }

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => $product->loadMissing([
                    'translations',
                    'metaTags',
                    'stocks.addons',
                    'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                ])
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $products = Product::whereIn('id', $ids)
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->get();

        $errorIds = [];

        foreach ($products as $product) {
            try {
                $product->delete();
            } catch (Throwable $e) {
                if (!empty($e->getMessage())) { // this if only for vercel test demo
                    $errorIds[] = $product->id;
                }
            }
        }

        if (count($errorIds) === 0) {
            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_505,
            'message' => __(
                'errors.' . ResponseError::CANT_DELETE_IDS,
                [
                    'ids' => implode(', ', $errorIds)
                ],
                $this->language
            )
        ];
    }

    /**
     * @param Stock $stock
     * @param array $ids
     * @return array
     */
    public function syncAddons(Stock $stock, array $ids): array
    {
        $errIds = [];

        if (count($ids) === 0) {
            return $errIds;
        }

        try {

            $stock->addons()->delete();

            foreach ($ids as $id) {

                /** @var Product $product */
                $product = Product::with('stock')->where('id', $id)->first();

                if (
                    data_get($product,'stock.addon') !== 1 ||
                    $product->shop_id !== $stock->countable?->shop_id ||
                    $product->stock?->bonus
                ) {
                    $errIds[] = $id;
                    continue;
                }

                $stock->addons()->create([
                    'addon_id' => $id
                ]);

            }

        } catch (Throwable $e) {

            $this->error($e);
            $errIds = $ids;
        }

        return $errIds;
    }

    private function checkIsParentCategory(int $categoryId): bool
    {
        $parentCategory = Category::firstWhere('parent_id', $categoryId);

        return !!data_get($parentCategory, 'id');
    }
}
