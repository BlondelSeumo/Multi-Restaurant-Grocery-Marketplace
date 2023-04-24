<?php

namespace App\Services\ShopServices;

use App\Helpers\ResponseError;
use App\Models\ShopDeliverymanSetting;
use App\Services\CoreService;
use Exception;
use Throwable;

class ShopDeliverymanSettingService extends CoreService
{
    protected function getModelClass(): string
    {
        return ShopDeliverymanSetting::class;
    }

    /**
     * Create a new Shop model.
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $model = $this->model()->create($data);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model
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
     * Update specified Shop model.
     * @param ShopDeliverymanSetting $model
     * @param array $data
     * @return array
     */
    public function update(ShopDeliverymanSetting $model, array $data): array
    {
        try {
            $model->update($data);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model
            ];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    /**
     * Delete Shop model.
     * @param array|null $ids
     * @param int|null $shopId
     * @return array
     */
    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $models = ShopDeliverymanSetting::whereIn('id', is_array($ids) ? $ids : [])
            ->when(!empty($shopId), fn($q) => $q->where('shop_id', $shopId))
            ->get();

        foreach ($models as $model) {
            $model->delete();
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }
}
