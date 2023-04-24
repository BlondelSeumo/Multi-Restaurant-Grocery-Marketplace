<?php

namespace App\Services\DeliveryZoneService;

use App\Helpers\ResponseError;
use App\Models\DeliveryZone;
use App\Services\CoreService;
use Exception;
use Illuminate\Support\LazyCollection;

class DeliveryZoneService extends CoreService
{

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return DeliveryZone::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            // Если нужна строгая типизация можно включить
            //$data['address'] = $this->latLongToDouble($data);

            $deliveryZone = $this->model()->create($data);

            try {
                cache()->forget('delivery-zone-list');
            } catch (Exception) {}

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryZone];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param DeliveryZone $deliveryZone
     * @param array $data
     * @return array
     */
    public function update(DeliveryZone $deliveryZone, array $data): array
    {
        try {
            // Если нужна строгая типизация можно включить
            //$data['address'] = $this->latLongToDouble($data);

            $deliveryZone->update($data);

            try {
                cache()->forget('delivery-zone-list');
            } catch (Exception $e) {}

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $deliveryZone];
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
     * @param array|null $ids
     * @param int|null $shopId
     * @return array
     */
    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $deliveryZones = DeliveryZone::whereIn('id', is_array($ids) ? $ids : [])
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->get();

        foreach ($deliveryZones as $deliveryZone) {
            $deliveryZone->delete();
        }

        try {
            cache()->forget('delivery-zone-list');
        } catch (Exception) {}

        return [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function latLongToDouble(array $data): array
    {
        return LazyCollection::make(function () use ($data) {
            foreach (data_get($data, 'address', []) as $address) {
                yield [(double)data_get($address, '0'), (double)data_get($address, '1')];
            }
        })->all();
    }

}
