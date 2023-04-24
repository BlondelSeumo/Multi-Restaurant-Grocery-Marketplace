<?php

namespace App\Services\ShopGalleryService;

use App\Helpers\ResponseError;
use App\Models\ShopGallery;
use App\Services\CoreService;
use DB;
use Throwable;

class ShopGalleryService extends CoreService
{
    protected function getModelClass(): string
    {
        return ShopGallery::class;
    }

    public function create(array $data): array
    {
        try {

            $model = DB::transaction(function () use ($data) {
                /** @var ShopGallery $model */

                $model = $this->model()->updateOrCreate([
                    'shop_id' => data_get($data, 'shop_id')
                ], $data);

                if (data_get($data, 'images.0')) {
                    $model->uploads(data_get($data, 'images'));
                }

                return $model;
            });

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model,
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

    public function update(ShopGallery $model, array $data): array
    {
        try {

            DB::transaction(function () use ($model, $data) {

                $model->update($data);

                if (data_get($data, 'images.0')) {
                    $model->galleries()->delete();
                    $model->uploads(data_get($data, 'images'));
                }

            });

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model,
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function deleteOne(int $id, ?int $shopId = null): array
    {
        try {
            /** @var ShopGallery $model */
            $model = ShopGallery::with(['galleries'])->when($shopId, fn($q) => $q->where('shop_id', $shopId))->find($id);

            $model?->galleries()?->delete();
            $model?->delete();

            return [
                'status'  => true,
                'code'    => ResponseError::NO_ERROR,
                'message' => __('errors.' . ResponseError::NO_ERROR, locale: $this->language)
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_505,
                'message' => __('errors.' . ResponseError::ERROR_505, locale: $this->language)
            ];
        }
    }

}
