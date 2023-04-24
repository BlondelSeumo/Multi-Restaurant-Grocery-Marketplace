<?php

namespace App\Services\DeliveryManSettingService;

use App\Helpers\ResponseError;
use App\Models\DeliveryManSetting;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use DB;
use Exception;
use Throwable;

class DeliveryManSettingService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return DeliveryManSetting::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $deliveryManSetting = DB::transaction(function () use ($data) {

                /** @var DeliveryManSetting $deliveryManSetting */
                $deliveryManSetting = $this->model()->updateOrCreate([
                    'user_id' => data_get($data, 'user_id')
                ], $data);

                $this->setTranslations($deliveryManSetting, $data);

                if (data_get($data, 'images.0')) {
                    $deliveryManSetting->uploads(data_get($data, 'images'));
                    $deliveryManSetting->update(['img' => data_get($data, 'images.0')]);
                }

                return $deliveryManSetting;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryManSetting];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function update(DeliveryManSetting $deliveryManSetting, array $data): array
    {
        try {

            $deliveryManSetting = DB::transaction(function () use ($deliveryManSetting, $data) {

                $deliveryManSetting->update($data);

                $this->setTranslations($deliveryManSetting, $data);

                if (data_get($data, 'images.0')) {
                    $deliveryManSetting->galleries()->delete();
                    $deliveryManSetting->uploads(data_get($data, 'images'));
                    $deliveryManSetting->update(['img' => data_get($data, 'images.0')]);
                }

                return $deliveryManSetting;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryManSetting];

        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function createOrUpdate(array $data): array
    {
        try {
            $deliveryManSetting = DB::transaction(function () use ($data) {
                $data['user_id'] = auth('sanctum')->id();

                /** @var DeliveryManSetting $deliveryManSetting */
                $deliveryManSetting = $this->model()->updateOrCreate([
                    'user_id' => $data['user_id']
                ], $data);

                $this->setTranslations($deliveryManSetting, $data);

                if (data_get($data, 'images.0')) {
                    $deliveryManSetting->galleries()->delete();
                    $deliveryManSetting->uploads(data_get($data, 'images'));
                    $deliveryManSetting->update(['img' => data_get($data, 'images.0')]);
                }

                return $deliveryManSetting->loadMissing(['galleries', 'deliveryMan']);
            });

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $deliveryManSetting
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

    public function updateLocation(array $data): array
    {
        try {
            $deliveryManSetting = DeliveryManSetting::where('user_id', auth('sanctum')->id())->first();

            $deliveryManSetting->update($data + ['updated_at' => now()]);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryManSetting];

        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function updateOnline(): array
    {
        try {
            $deliveryManSetting = DeliveryManSetting::where('user_id', auth('sanctum')->id())->first();

            $deliveryManSetting->update([
               'online' => !$deliveryManSetting->online
            ]);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryManSetting];

        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function destroy(?array $ids = []) {

        $deliveryManSettings = DeliveryManSetting::whereIn('id', is_array($ids) ? $ids : [])->get();

        foreach ($deliveryManSettings as $deliveryManSetting) {
            $deliveryManSetting->delete();
        }

    }
}
