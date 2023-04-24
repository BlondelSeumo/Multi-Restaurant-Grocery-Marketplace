<?php

namespace App\Services\DiscountService;

use App\Helpers\ResponseError;
use App\Models\Discount;
use App\Services\CoreService;
use DB;
use Exception;
use Throwable;

class DiscountService extends CoreService
{
    protected function getModelClass(): string
    {
        return Discount::class;
    }

    public function create(array $data): array
    {
        try {

            $discount = DB::transaction(function () use ($data) {
                /** @var Discount $discount */
                $this->model()->create($data);

                if (!empty(data_get($data, 'products.*'))) {
                    $discount->products()->sync(data_get($data, 'products'));
                }

                if (data_get($data, 'images.0')) {

                    $discount->uploads(data_get($data, 'images'));
                    $discount->update([
                        'img' => data_get($data, 'images.0')
                    ]);

                }
                return $discount;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $discount];
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
     * @param Discount $discount
     * @param array $data
     * @return array
     */
    public function update(Discount $discount, array $data): array
    {
        try {
            $discount = DB::transaction(function () use ($discount, $data) {

                $discount->update($data);

                if (!empty(data_get($data, 'products.*'))) {
                    $discount->products()->sync(data_get($data, 'products'));
                }

                if (data_get($data, 'images.0')) {
                    $discount->galleries()->delete();
                    $discount->uploads(data_get($data, 'images'));
                    $discount->update([
                        'img' => data_get($data, 'images.0')
                    ]);

                }

                return $discount;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $discount];
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
