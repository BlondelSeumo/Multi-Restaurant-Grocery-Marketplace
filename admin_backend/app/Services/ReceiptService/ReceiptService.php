<?php

namespace App\Services\ReceiptService;

use App\Helpers\ResponseError;
use App\Models\Category;
use App\Models\Receipt;
use App\Models\ReceiptNutrition;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use DB;
use Throwable;

class ReceiptService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Receipt::class;
    }

    public function create(array $data): array
    {
        try {

            if (
                !empty(data_get($data, 'category_id')) &&
                $this->checkIsParentCategory(data_get($data, 'category_id'))
            ) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_501,
                    'message' => __('errors.' . ResponseError::CATEGORY_IS_PARENT, locale: $this->language)
                ];
            }

            /** @var Receipt $model */
            $type                   = data_get($data, 'discount_type', 'fix');
            $data['discount_type']  = data_get(Receipt::DISCOUNT_TYPES, $type, 0);
            $data['img']            = data_get($data, 'images.0');

            $model = DB::transaction(function () use ($data) {
                $model = $this->model()->create($data);

                //translations
                $this->setTranslations($model, $data);

                //ingredients
                $this->setRelations($model, $data);

                //instructions
                $this->setRelations($model, $data, 'instruction');

                //nutrition
                $this->setRelations($model, $data, 'nutrition');

                if (data_get($data, 'images.0')) {
                    $model->uploads(data_get($data, 'images'));
                }

                $model->stocks()->sync(data_get($data, 'stocks'));

                return $model;
            });

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model,
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

    public function update(Receipt $model, array $data): array
    {
        try {
            $type                   = data_get($data, 'discount_type', 'fix');
            $data['discount_type']  = data_get(Receipt::DISCOUNT_TYPES, $type, 0);
            $data['img']            = data_get($data, 'images.0');

            DB::transaction(function () use ($model, $data) {

                $model->update($data);

                //translations
                $this->setTranslations($model, $data);

                //ingredients
                $this->setRelations($model, $data);

                //instructions
                $this->setRelations($model, $data, 'instruction');

                //nutrition
                $this->setRelations($model, $data, 'nutrition');

                $model->stocks()->sync(data_get($data, 'stocks'));

                if (data_get($data, 'images.0')) {
                    $model->galleries()->delete();
                    $model->uploads(data_get($data, 'images'));
                }

            });

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $model,
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
        $models = Receipt::whereIn('id', is_array($ids) ? $ids : [])
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->get();

        $errorIds = [];

        foreach ($models as $model) {
            /** @var Receipt $model */
            try {
                DB::transaction(function () use ($model) {
                    $model->stocks()->delete();
                    $model->translations()->delete();
                    $model->ingredients()->delete();
                    $model->instructions()->delete();
                    $model->nutritions()->delete();
                    $model->delete();
                });
            } catch (Throwable $e) {
                $this->error($e);
                $errorIds[] = $model->id;
            }
        }

        if (count($errorIds) === 0) {
            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        }

        return [
            'status'  => ResponseError::ERROR_400,
            'message' => __('errors.' . ResponseError::CANT_DELETE_ORDERS, [
                'ids' => implode(', #', $errorIds)
            ], locale: $this->language)
        ];
    }

    /**
     * @param Receipt $model
     * @param array $data
     * @param string $key
     * @return void
     */
    public function setRelations(
        Receipt $model,
        array $data,
        string $key = 'ingredient'
    ): void
    {
        $relations = $key . 's';

        if (is_array(data_get($data, $key))) {
            $model->$relations()->forceDelete();
        }

        foreach (is_array(data_get($data, $key)) ? data_get($data, $key) : [] as $index => $value) {

            if ($key !== 'nutrition') {

                $model->$relations()->create([
                    'title'     => $value,
                    'locale'    => $index,
                ]);

                continue;
            }

            $nutrition = $model->nutritions()->create([
                'weight'     => data_get($value, 'weight'),
                'percentage' => data_get($value, 'percentage'),
            ]);

            unset($value['weight']);
            unset($value['percentage']);

            foreach ($value as $locale => $title) {

                /** @var ReceiptNutrition $nutrition */
                $nutrition->translations()->create([
                    'locale' => $locale,
                    'title'  => $title
                ]);

            }

        }
    }

    private function checkIsParentCategory(int $categoryId): bool
    {
        $parentCategory = Category::firstWhere('parent_id', $categoryId);

        return !!data_get($parentCategory, 'id');
    }

}
