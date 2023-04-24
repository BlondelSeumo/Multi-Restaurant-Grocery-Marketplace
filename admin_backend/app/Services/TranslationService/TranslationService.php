<?php

namespace App\Services\TranslationService;

use App\Helpers\ResponseError;
use App\Models\Subscription;
use App\Models\Translation;
use App\Services\CoreService;
use Exception;
use Throwable;

class TranslationService extends CoreService
{
    protected function getModelClass(): string
    {
        return Subscription::class;
    }

    public function create(array $data): array
    {

        Translation::where('key', data_get($data, 'key'))->forceDelete();

        try {

            $value = data_get($data, 'value');

            foreach (is_array($value) ? $value : [] as $index => $item) {

                Translation::create([
                    'group'     => data_get($data, 'group'),
                    'key'       => data_get($data, 'key'),
                    'locale'    => $index,
                    'status'    => data_get($data, 'status', 1),
                    'value'     => $item,
                ]);

                try {
                    cache()->forget('language-' . $index);
                } catch (Throwable $e) {
                    $this->error($e);
                }

            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function update(array $data): array
    {
        try {
            Translation::where('key', data_get($data, 'key'))->forceDelete();

            $value = data_get($data, 'value');

            foreach (is_array($value) ? $value : [] as $index => $item) {

                Translation::create([
                    'group'     => data_get($data, 'group'),
                    'key'       => data_get($data, 'key'),
                    'locale'    => $index,
                    'value'     => $item,
                ]);

                try {
                    cache()->forget('language-' . $index);
                } catch (Throwable $e) {
                    $this->error($e);
                }

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

}
