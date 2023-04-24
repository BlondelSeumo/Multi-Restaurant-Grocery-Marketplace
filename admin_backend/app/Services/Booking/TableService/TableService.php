<?php

namespace App\Services\Booking\TableService;

use App\Helpers\ResponseError;
use App\Models\Booking\Table;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class TableService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Table::class;
    }

    public function create(array $data): array
    {
        try {
            /** @var Table $model */
            $model = $this->model()->create($data);

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => $model,
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

    public function update(Table $model, array $data): array
    {
        try {
            $model->update($data);

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => $model,
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

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        $models = Table::with([
            'shopSection' => fn($q) => $q->where('shop_id', $shopId)
        ])
            ->whereIn('id', is_array($ids) ? $ids : [])
            ->when($shopId, fn($q) => $q->whereHas('shopSection', fn($q) => $q->where('shop_id', $shopId)))
            ->get();

        $errorIds = [];

        foreach ($models as $model) {

            /** @var Table $model */

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
