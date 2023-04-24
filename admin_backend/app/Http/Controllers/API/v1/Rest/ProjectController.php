<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Services\ProjectService\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends RestBaseController
{
    public function licenceCheck(): JsonResponse
    {
        $response = (new ProjectService)->activationKeyCheck();

        $response = json_decode($response);

        if ($response->key == config('credential.purchase_code') && $response->active) {
            return $this->successResponse(
                trans('errors.' . ResponseError::NO_ERROR, [], $this->language),
                $response
            );
        }

        return $this->onErrorResponse(['code' => ResponseError::ERROR_403]);
    }
}
