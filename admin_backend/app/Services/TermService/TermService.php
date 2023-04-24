<?php

namespace App\Services\TermService;

use App\Helpers\ResponseError;
use App\Models\TermCondition;
use App\Services\CoreService;
use App\Traits\SetTranslations;
use DB;
use Throwable;

class TermService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return TermCondition::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $term = DB::transaction(function () use ($data) {
                $term = $this->model()->create();

                $this->setTranslations($term, $data);

                return $term;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $term];
        } catch (Throwable $e) {
            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => $e->getMessage(),
            ];
        }
    }

}
