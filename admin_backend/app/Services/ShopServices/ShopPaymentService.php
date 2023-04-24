<?php

namespace App\Services\ShopServices;

use App\Helpers\ResponseError;
use App\Models\ShopPayment;
use App\Services\CoreService;
use Throwable;

class ShopPaymentService extends CoreService
{
    protected function getModelClass(): string
    {
        return ShopPayment::class;
    }

    public function create(array $data): array
    {
        try {
            $this->model()->create($data);

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function update(array $data, ShopPayment $shopPayment): array
    {
        try {
            $shopPayment->update($data);

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
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
        foreach ($this->model()->find(is_array($ids) ? $ids : []) as $value) {
            /** @var ShopPayment $value */
            if ($value->shop_id === $shopId) {
                $value->delete();
            }
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }

    public function setActive(int $id, int $shopId): array
    {
        try {
            $shopPayment = ShopPayment::find($id);

            if ($shopPayment->shop_id !== $shopId) {
                return ['status' => false, 'code' => ResponseError::ERROR_204];
            }

            $shopPayment->update([
                'active' => !$shopPayment->status
            ]);

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }
}
