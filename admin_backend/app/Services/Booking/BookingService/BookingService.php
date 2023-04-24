<?php

namespace App\Services\Booking\BookingService;

use App\Helpers\ResponseError;
use App\Models\Booking\Booking;
use App\Services\CoreService;
use Throwable;

class BookingService extends CoreService
{
    protected function getModelClass(): string
    {
        return Booking::class;
    }

    public function create(array $data): array
    {
        try {
            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $this->model()->create($data),
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

    public function update(Booking $model, array $data): array
    {
        try {
            $model->update($data);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model,
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_502,
                'message'   => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $models = Booking::whereIn('id', is_array($ids) ? $ids : [])
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->get();

        $errorIds = [];

        foreach ($models as $model) {
            /** @var Booking $model */
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
