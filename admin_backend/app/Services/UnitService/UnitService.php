<?php

namespace App\Services\UnitService;

use App\Helpers\ResponseError;
use App\Models\Unit;
use App\Services\CoreService;
use DB;
use Throwable;

class UnitService extends CoreService
{
    protected function getModelClass(): string
    {
        return Unit::class;
    }

    public function create(array $data): array
    {
        try {

            $unitId = DB::transaction(function () use ($data) {

                /** @var Unit $unit */
                $unit = $this->model()->create([
                    'active'    => data_get($data, 'active', 0),
                    'position'  => data_get($data, 'position', 'after'),
                ]);

                $unit->translations()->forceDelete();

                $title = data_get($data, 'title');

                foreach (is_array($title) ? $title : [] as $index => $value) {

                    $unit->translation()->create([
                        'locale' => $index,
                        'title'  => $value,
                    ]);

                }

                return $unit->id;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => Unit::find($unitId)];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_501,
            'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function update(Unit $unit, array $data): array
    {
        try {
            $unit->update([
                'active' => data_get($data, 'active', 0),
                'position' => data_get($data, 'position', 'after'),
            ]);

            $unit->translations()->forceDelete();

            $title = data_get($data, 'title');

            foreach (is_array($title) ? $title : [] as $index => $value) {

                $unit->translation()->create([
                    'locale' => $index,
                    'title' => $value,
                ]);

            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => Unit::find($unit->id)];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_502,
            'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
        ];
    }

    public function setActive($id): array
    {
        try {
            $unit = Unit::find($id);

            $unit->update(['active' => !$unit->active]);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => Unit::find($unit->id)];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_502,
            'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
        ];
    }
}
