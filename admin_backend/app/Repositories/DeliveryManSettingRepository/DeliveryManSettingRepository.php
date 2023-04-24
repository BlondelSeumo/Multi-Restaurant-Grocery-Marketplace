<?php

namespace App\Repositories\DeliveryManSettingRepository;

use App\Models\DeliveryManSetting;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DeliveryManSettingRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return DeliveryManSetting::class;
    }

    public function paginate(array $filter): LengthAwarePaginator
    {
        /** @var DeliveryManSetting $deliveryManSetting */
        $deliveryManSetting = $this->model();

        return $deliveryManSetting
            ->with('deliveryMan:id,uuid,active,firstname,lastname,phone,img')
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function detail(?int $id = null, ?int $userId = null): ?DeliveryManSetting
    {
        /** @var DeliveryManSetting $deliveryManSetting */
        $deliveryManSetting = $this->model();

        return $deliveryManSetting->with('deliveryMan', 'galleries')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($id, fn($q) => $q->where('id', $id))
            ->first();
    }

}
