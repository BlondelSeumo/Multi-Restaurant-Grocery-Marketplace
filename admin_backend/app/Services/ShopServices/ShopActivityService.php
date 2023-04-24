<?php

namespace App\Services\ShopServices;

use App\Helpers\ResponseError;
use App\Models\Shop;
use App\Services\CoreService;

class ShopActivityService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Shop::class;
    }

    public function changeStatus(string $uuid,  $status): array
    {
        /** @var Shop $shop */
        $shop = $this->model()->firstWhere('uuid', $uuid);

        if (!$shop) {
            return [
                'status'  => false,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        if ($shop->seller->hasRole('admin')) {
            return [
                'status'  => false,
                'message' => __('errors.' . ResponseError::ERROR_207, locale: $this->language)
            ];
        }

        $shop->update(['status' => $status]);

        if ($status == 'approved') {
            $shop->seller->syncRoles('seller');
        }

        return ['status' => true, 'message' => ResponseError::NO_ERROR, 'data' => $shop];
    }

    public function changeOpenStatus(string $uuid)
    {
        $shop = $this->model()->firstWhere('uuid', $uuid);

        $shop->update(['open' => !$shop->open]);
    }

}
