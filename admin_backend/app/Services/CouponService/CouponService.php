<?php

namespace App\Services\CouponService;

use App\Helpers\ResponseError;
use App\Models\Coupon;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Exception;

class CouponService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Coupon::class;
    }

    public function create(array $data): array
    {
        try {
            $coupon = $this->model()->create($data);

            $this->setTranslations($coupon, $data);

            if ($coupon && data_get($data, 'images.0')) {
                $coupon->update(['img' => data_get($data, 'images.0')]);
                $coupon->uploads(data_get($data, 'images'));
            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $coupon];
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
     * @param Coupon $coupon
     * @param array $data
     * @return array
     */
    public function update(Coupon $coupon, array $data): array
    {
        try {
            $coupon->update($data);

            $this->setTranslations($coupon, $data);

            if (data_get($data, 'images.0')) {
                $coupon->galleries()->delete();
                $coupon->update(['img' => data_get($data, 'images.0')]);
                $coupon->uploads(data_get($data, 'images'));
            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $coupon];
        }
        catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

}
