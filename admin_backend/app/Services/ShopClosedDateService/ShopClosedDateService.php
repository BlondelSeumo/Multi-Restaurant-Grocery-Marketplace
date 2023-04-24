<?php

namespace App\Services\ShopClosedDateService;

use App\Helpers\ResponseError;
use App\Models\Shop;
use App\Models\ShopClosedDate;
use App\Services\CoreService;
use Throwable;

class ShopClosedDateService extends CoreService
{
    protected function getModelClass(): string
    {
        return ShopClosedDate::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {

            foreach (data_get($data, 'dates', []) as $date) {

                $exist = ShopClosedDate::where([
                    ['shop_id', data_get($data, 'shop_id')],
                    ['date', $date]
                ])->exists();

                if ($exist) {
                    continue;
                }

                $this->model()->create(['shop_id' => data_get($data, 'shop_id'), 'date' => $date]);
            }

            return [
                'status'  => true,
                'message' => ResponseError::NO_ERROR,
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

    public function update(int $shopId, array $data): array
    {
        try {

            Shop::find($shopId)->closedDates()->delete();

            $dates = data_get($data, 'dates');

            foreach (is_array($dates) ? $dates : []  as $date) {

                ShopClosedDate::create(['shop_id' => $shopId, 'date' => $date]);

            }

            return [
                'status'  => true,
                'message' => ResponseError::NO_ERROR,
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

    public function delete(?array $ids = [], ?int $shopId = null) {

        $shopClosedDates = ShopClosedDate::when($shopId, fn($q, $shopId) => $q->where('shop_id', $shopId))
            ->find(is_array($ids) ? $ids : []);

        foreach ($shopClosedDates as $shopClosedDate) {
            $shopClosedDate->delete();
        }

    }
}
