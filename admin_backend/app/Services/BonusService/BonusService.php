<?php

namespace App\Services\BonusService;

use App\Helpers\ResponseError;
use App\Models\Bonus;
use App\Services\CoreService;

class BonusService extends CoreService
{
    protected function getModelClass(): string
    {
        return Bonus::class;
    }

    public function create(array $collection): array
    {
        $bonus = $this->model()->create($collection);

        /** @var Bonus $bonus */

        if (!$bonus) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }

    public function update(Bonus $bonus, array $data): array
    {
        $bonus->update($data);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
        ];
    }

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $bonuses = Bonus::whereIn('id', is_array($ids) ? $ids : [])
            ->where('shop_id', $shopId)
            ->get();

        foreach ($bonuses as $bonus) {
            $bonus->delete();
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }

    public function statusChange(int $id): array
    {
        /** @var Bonus $bonus */
        $bonus = $this->model()->find($id);

        if (!$bonus) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $bonus->update(['status' => !$bonus->status]);

        return [
            'status'    => true,
            'code'      => ResponseError::NO_ERROR,
            'data'      => $bonus->loadMissing(['bonusable', 'stock'])
        ];
    }
}
