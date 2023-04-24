<?php

namespace App\Repositories\DeliveryZoneRepository;

use App\Models\DeliveryZone;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class DeliveryZoneRepository extends CoreRepository
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
     * @return LengthAwarePaginator
     */
    public function paginate(array $data = []): LengthAwarePaginator
    {
        /** @var DeliveryZone $deliveryZones */
        $deliveryZones = $this->model();

        return $deliveryZones
            ->with([
                'shop.translation' => fn($q) => $q->where('locale', $this->language)
            ])
            ->when(data_get($data, 'shop_id'), fn(Builder $q, $shopId) => $q->where('shop_id', $shopId))
            ->when(isset($data['deleted_at']), fn($q) => $q->onlyTrashed())
            ->orderBy(data_get($data, 'column', 'id'), data_get($data, 'sort', 'desc'))
            ->paginate(data_get($data, 'perPage', 15));
    }

    /**
     * @param DeliveryZone $deliveryZone
     * @return DeliveryZone
     */
    public function show(DeliveryZone $deliveryZone): DeliveryZone
    {
        return $deliveryZone->load([
            'shop.translation' => fn($q) => $q->where('locale', $this->language)
        ]);
    }
}
