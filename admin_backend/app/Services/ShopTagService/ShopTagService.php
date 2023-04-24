<?php

namespace App\Services\ShopTagService;

use App\Helpers\ResponseError;
use App\Models\ShopTag;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class ShopTagService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return ShopTag::class;
    }

    public function create(array $data): array
    {
        try {
            $shopTag = $this->model()->create($data);
            $this->setTranslations($shopTag, $data);
            if (data_get($data, 'images.0')) {
                $shopTag->uploads(data_get($data, 'images'));
                $shopTag->update(['img' => data_get($data, 'images.0')]);
            }
            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => $shopTag,
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

    public function update(ShopTag $shopTag, array $data): array
    {
        try {
            $shopTag->update($data);
            $this->setTranslations($shopTag, $data);
            if (data_get($data, 'images.0')) {
                $shopTag->uploads(data_get($data, 'images'));
                $shopTag->update(['img' => data_get($data, 'images.0')]);
            }
            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
                'data' => $shopTag,
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_502,
            'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
        ];
    }

    public function delete(?array $ids = []): array
    {
        $shopTags = ShopTag::whereIn('id', is_array($ids) ? $ids : [])->get();

        foreach ($shopTags as $shopTag) {

            /** @var ShopTag $shopTag */
            $shopTag->translations()->forceDelete();
            $shopTag->delete();

        }

        return [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
        ];
    }

}
