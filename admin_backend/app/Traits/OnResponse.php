<?php

namespace App\Traits;

use App\Helpers\ResponseError;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait OnResponse
{
    /**
     * @param array $result = ['code' => 200]
     * @return JsonResponse
     */
    public function onErrorResponse(array $result = []): JsonResponse
    {
        $code = data_get($result, 'code', ResponseError::ERROR_101);

        $httpDefault = $code === ResponseError::ERROR_404 ? Response::HTTP_NOT_FOUND : Response::HTTP_BAD_REQUEST;

        $http = data_get($result, 'http', $httpDefault);

        $data = is_array(data_get($result, 'data')) ? data_get($result, 'data') : [];

        $message = $code === ResponseError::ERROR_101 ?
            __('errors.' . ResponseError::ERROR_101, $data, request('lang', 'en')) :
            __('errors.' . $code, $data, request('lang', 'en'));

        return $this->errorResponse($code, data_get($result, 'message', $message), $http);
    }
}
