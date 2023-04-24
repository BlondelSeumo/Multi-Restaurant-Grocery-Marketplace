<?php

namespace App\Services\BrandService;

use App\Helpers\ResponseError;
use App\Models\Brand;
use App\Services\CoreService;
use App\Services\Interfaces\BrandServiceInterface;
use Throwable;

class BrandService extends CoreService implements BrandServiceInterface
{
    protected function getModelClass(): string
    {
        return Brand::class;
    }

    public function create($data): array
    {
        try {
            /** @var Brand $brand */
            $brand = $this->model()->create($data);

            if (data_get($data, 'meta')) {
                $brand->setMetaTags($data);
            }

            if (data_get($data, 'images.0')) {

                $brand->update([
                    'img' => data_get($data, 'images.0')
                ]);

                $brand->uploads(data_get($data, 'images'));

            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $brand];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_501,
                'message'   => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }

    }

    public function update(Brand $brand, array $data): array
    {
        try {
            $brand->update($data);

            if (data_get($data, 'meta')) {
                $brand->setMetaTags($data);
            }

            if (data_get($data, 'images.0')) {

                $brand->galleries()->delete();

                $brand->update([
                    'img' => data_get($data, 'images.0')
                ]);

                $brand->uploads(data_get($data, 'images'));

            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $brand];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' =>  __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function delete(?array $ids = []): array
    {
        $hasProducts = 0;

        foreach (Brand::whereIn('id', is_array($ids) ? $ids : [])->get() as $brand) {

            /** @var Brand $brand */

            if (count($brand->products) > 0) {
                $hasProducts++;
                continue;
            }

            $brand->delete();
        }

        return [
            'status'  => true,
            'code'    => ResponseError::ERROR_507,
            'message' => __('errors.' . ResponseError::ERROR_507, locale: $this->language)
        ] + ($hasProducts ? ['data' => $hasProducts] : []);
    }
}
