<?php

namespace App\Services\CategoryServices;

use App\Helpers\ResponseError;
use App\Models\Category;
use App\Services\CoreService;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Traits\SetTranslations;
use DB;
use Throwable;

class CategoryService extends CoreService implements CategoryServiceInterface
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Category::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data = []): array
    {
        try {
            DB::transaction(function () use ($data) {
                /** @var Category $category */
                $data['type'] = data_get(Category::TYPES, data_get($data, 'type', 1));
                $category = $this->model()->create($data);

                if (is_array(data_get($data, 'meta'))) {
                    $category->setMetaTags($data);
                }

                $this->setTranslations($category, $data);

                if ($category && data_get($data, 'images.0')) {
                    $category->update(['img' => data_get($data, 'images.0')]);
                    $category->uploads(data_get($data, 'images'));
                }
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return array
     */
    public function update(string $uuid, array $data = []): array
    {
        try {
            $category = $this->model()->firstWhere('uuid', $uuid);

            $data['type'] = data_get(Category::TYPES, data_get($data, 'type', 1));

            $category->update($data);

            if (data_get($data, 'meta')) {
                $category->setMetaTags($data);
            }

            $this->setTranslations($category, $data);

            if (data_get($data, 'images.0')) {
                $category->galleries()->delete();
                $category->galleries()->delete();
                $category->update(['img' => data_get($data, 'images.0')]);
                $category->uploads(data_get($data, 'images'));
            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Throwable $e) {
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
     * @return array
     */
    public function delete(?array $ids = []): array
    {
        $hasChildren = 0;

        $categories = Category::with('children')->whereIn('id', is_array($ids) ? $ids : [])->get();

        foreach ($categories as $category) {

            /** @var Category $category */
            try {
                if (count($category->children) > 0) {
                    $hasChildren++;
                    continue;
                }

                $category->delete();
            } catch (Throwable) {
                $hasChildren++;
                continue;
            }
        }

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR
        ] + ($hasChildren ? ['data' => $hasChildren] : []);
    }

}
