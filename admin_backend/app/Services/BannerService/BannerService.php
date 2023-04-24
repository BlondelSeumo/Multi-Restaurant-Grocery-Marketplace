<?php

namespace App\Services\BannerService;

use App\Helpers\ResponseError;
use App\Models\Banner;
use App\Services\CoreService;
use DB;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class BannerService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Banner::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $banner = DB::transaction(function () use ($data) {
                /** @var Banner $banner */
                $banner = $this->model()->create($data);
                $banner->shops()->sync(data_get($data, 'shops', []));
                $this->setTranslations($banner, $data);

                if (data_get($data, 'images.0')) {
                    $banner->uploads(data_get($data, 'images'));
                    $banner->update(['img' => data_get($data, 'images.0')]);
                }

                return $banner;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $banner];

        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'    => false,
            'code'      => ResponseError::ERROR_501,
            'message'   => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function update(Banner $banner, array $data): array
    {
        try {
            DB::transaction(function () use ($banner, $data) {
                $banner->update($data);
                $banner->shops()->sync(data_get($data, 'shops', []));
                $this->setTranslations($banner, $data);

                if (data_get($data, 'images.0')) {
                    $banner->galleries()->delete();
                    $banner->uploads(data_get($data, 'images'));
                    $banner->update(['img' => data_get($data, 'images.0')]);
                }
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $banner];

        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function destroy(?array $ids = [], ?int $shopId = null) {

        /** @var Banner $banners */
        $banners = $this->model();

        foreach ($banners->whereIn('id', is_array($ids) ? $ids : [])->get() as $banner) {

            /** @var Banner $banner */

            $sync = $banner->shops->pluck('id')->toArray();

            if (!empty($shopId)) {
                unset($sync[$shopId]);
            }

            $banner->shops()->sync($sync);

            if (empty($shopId) || count($sync)) {
                $banner->galleries()->delete();
                $banner->delete();
            }

        }

    }

    /**
     * @param Banner $model
     * @param array $data
     * @return void
     */
    public function setTranslations(Model $model, array $data): void
    {
        try {

            if (is_array(data_get($data, 'title'))) {
                $model->translations()->forceDelete();
            }

            foreach (is_array(data_get($data, 'title')) ? data_get($data, 'title') : [] as $index => $value) {

                $model->translation()->create([
                        'title'         => $value,
                        'locale'        => $index,
                        'description'   => data_get($data, "description.$index"),
                        'button_text'   => data_get($data, "button_text.$index"),
                    ]);
            }

        } catch (Throwable $e) {
            $this->error($e);
        }
    }

    public function setActiveBanner(int $id): array
    {
        /** @var Banner $banner */
        $banner = $this->model()->find($id);

        if (empty($banner)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $banner->update(['active' => !$banner->active]);

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $banner];
    }
}
