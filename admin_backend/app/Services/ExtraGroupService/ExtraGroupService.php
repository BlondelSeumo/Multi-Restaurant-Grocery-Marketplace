<?php

namespace App\Services\ExtraGroupService;

use App\Helpers\ResponseError;
use App\Models\ExtraGroup;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class ExtraGroupService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ExtraGroup::class;
    }


    public function create(array $data): array
    {
        try {
            /** @var ExtraGroup $extraGroup */
            $extraGroup = $this->model()->create($data);

            $this->setTranslations($extraGroup, $data, false);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $extraGroup,
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

    public function update(int $id, array $data): array
    {
        try {
            $extraGroup = ExtraGroup::find($id);
            $extraGroup->update($data);
            $this->setTranslations($extraGroup, $data, false);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $extraGroup,
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

    public function delete(?array $ids): int
    {
        $hasValues = 0;

        foreach ($this->model()->whereIn('id', is_array($ids) ? $ids : [])->get() as $extraGroup) {

            /** @var ExtraGroup $extraGroup */

            if (count($extraGroup->extraValues) > 0) {
                $hasValues++;
                continue;
            }

            $extraGroup->delete();
        }

        return $hasValues;
    }

    public function setActive(int $id): array
    {
        /** @var ExtraGroup $extraGroup */
        $extraGroup = ExtraGroup::find($id);

        if (empty($extraGroup)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $extraGroup->update(['active' => !$extraGroup->active]);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $extraGroup,
        ];
    }
}
