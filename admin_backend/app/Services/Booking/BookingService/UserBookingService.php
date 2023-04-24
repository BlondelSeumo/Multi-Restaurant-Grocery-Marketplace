<?php

namespace App\Services\Booking\BookingService;

use App\Helpers\ResponseError;
use App\Models\Booking\UserBooking;
use App\Services\CoreService;
use Throwable;

class UserBookingService extends CoreService
{
    protected function getModelClass(): string
    {
        return UserBooking::class;
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
                'status'    => false,
                'code'      => ResponseError::ERROR_501,
                'message'   => $e->getMessage() . ' | ' . $e->getCode() . ' | ' . $e->getLine()
            ];
        }
    }

    public function update(UserBooking $model, array $data): array
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
                'message'   => $e->getMessage() . ' | ' . $e->getCode() . ' | ' . $e->getLine()
            ];
        }
    }

    public function delete(?array $ids = [], ?int $userId = null): array
    {
        $models = UserBooking::whereIn('id', is_array($ids) ? $ids : [])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->get();

        $errorIds = [];

        foreach ($models as $model) {
            /** @var UserBooking $model */
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
