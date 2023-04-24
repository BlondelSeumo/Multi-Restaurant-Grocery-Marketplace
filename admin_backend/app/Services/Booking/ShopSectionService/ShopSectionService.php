<?php

namespace App\Services\Booking\ShopSectionService;

use App\Helpers\ResponseError;
use App\Models\Booking\ShopSection;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class ShopSectionService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return ShopSection::class;
    }

    public function create(array $data): array
    {
        try {
            /** @var ShopSection $model */
            $model = $this->model()->create($data);

            if (data_get($data, 'title.*')) {
                $this->setTranslations($model, $data);
            }

            if (data_get($data, 'images.0')) {
                $model->update(['img' => data_get($data, 'images.0')]);
                $model->uploads(data_get($data, 'images'));
            }

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $model,
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

    public function update(ShopSection $model, array $data): array
    {
        try {
            $model->update($data);

            if (data_get($data, 'title.*')) {
                $this->setTranslations($model, $data);
            }

            if (data_get($data, 'images.0')) {
                $model->galleries()->delete();
                $model->update(['img' => data_get($data, 'images.0')]);
                $model->uploads(data_get($data, 'images'));
            }

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

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $models = ShopSection::whereIn('id', is_array($ids) ? $ids : [])
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->get();

        $errorIds = [];

        foreach ($models as $model) {
            /** @var ShopSection $model */
            try {
                $model->delete();
            } catch (Throwable $e) {
                $this->error($e);
                $errorIds[] = $model->id;
            }
        }

        if (count($errorIds) === 0) {
            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_503,
            'message' => __('errors.' . ResponseError::CANT_DELETE_IDS, ['ids' => implode(', ', $errorIds)], $this->language)
        ];
    }

}
