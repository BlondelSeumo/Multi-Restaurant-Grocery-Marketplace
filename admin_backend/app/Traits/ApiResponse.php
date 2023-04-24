<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    use OnResponse, Loggable;

    /**
     * Success Response.
     *
     * @param string $message
     * @param mixed|null $data
     * @return JsonResponse
     */
    public function successResponse(string $message = '', $data = null): JsonResponse
    {
        return new JsonResponse([
            'timestamp' => now(),
            'status' => true,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }

    /**
     * Error Response.
     *
     * @param string $statusCode
     * @param string $message
     * @param int $httpCode
     * @return JsonResponse
     */
    public function errorResponse(string $statusCode, string $message = '', int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse([
            'timestamp' => now(),
            'status' => false,
            'statusCode' => $statusCode,
            'message' => $message
        ], $httpCode);
    }

    public function requestErrorResponse(string $statusCode, string $message = '', array $params = [], int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse([
            'timestamp' => now(),
            'status' => false,
            'statusCode' => $statusCode,
            'message' => $message,
            'params' => $params
        ], $httpCode);
    }

}
