<?php

namespace App\Services\PrivacyPolicyService;

use App\Helpers\ResponseError;
use App\Models\PrivacyPolicy;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use Throwable;

class PrivacyPolicyService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return PrivacyPolicy::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {

            $exist = PrivacyPolicy::first();

            $exist?->forceDelete();

            $privacyPolicy = $this->model()->create();

            $this->setTranslations($privacyPolicy, $data, true);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $privacyPolicy->load(['translations'])
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
}
