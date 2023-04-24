<?php

namespace App\Services\TagService;

use App\Helpers\ResponseError;
use App\Models\Tag;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class TagService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Tag::class;
    }

    public function create(array $data): array
    {
        try {
            $tag = $this->model()->create($data);

            $this->setTranslations($tag, $data);

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => [],
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_501,
            'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function update(Tag $tag, array $data): array
    {
        try {
            $tag->update($data);

            if (!empty(data_get($data, 'title'))) {
                $this->setTranslations($tag, $data);
            }

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => [],
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_501,
            'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function delete(?array $ids = [], ?int $shopId = null): array
    {
        foreach (Tag::whereIn('id', is_array($ids) ? $ids : [])->get() as $tag) {

            /** @var Tag $tag */

            if ($shopId && data_get($tag->product, 'shop_id') !== $shopId) {
                continue;
            }

            $this->setTranslations($tag, []);

            $tag->delete();
        }

        return [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
        ];
    }

}
