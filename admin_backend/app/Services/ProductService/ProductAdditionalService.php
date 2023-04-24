<?php

namespace App\Services\ProductService;

use App\Helpers\ResponseError;
use App\Models\Product;
use App\Services\CoreService;
use Exception;

class ProductAdditionalService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * @return mixed
     */
    public function createOrUpdateProperties(string $uuid, array $data): array
    {
        $item = $this->model()->firstWhere('uuid', $uuid);

        if (empty($item)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        try {

            $item->properties()->delete();

            $properties = data_get($data, 'key');

            foreach (is_array($properties) ? $properties : [] as $i => $keys) {

                foreach (is_array($keys) ? $keys : [] as $index => $key) {

                    $item->properties()->create([
                        'locale'    => $index,
                        'key'       => $key,
                        'value'     => data_get($data, "value.$i.$index")
                    ]);

                }

            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $item];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }

    }

    /**
     * @return mixed
     */
    public function createOrUpdateExtras(string $uuid, array $data): array
    {
        /** @var Product $product */
        $product = $this->model()->firstWhere('uuid', $uuid);

        if (empty($product)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        try {
            $extras = data_get($data, 'extras', []);

            $product->extras()->sync(is_array($extras) ? $extras : []);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => Product::find($product->id)];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }
}
