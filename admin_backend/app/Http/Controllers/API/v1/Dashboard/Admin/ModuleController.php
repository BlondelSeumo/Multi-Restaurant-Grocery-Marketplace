<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

/**
 * @author  Githubit
 * @email   support@githubit.com
 * @phone   +1 202 340 10-32
 * @site    https://githubit.com/
 */

use App\Helpers\ResponseError;
use App\Http\Requests\Module\BookingRequest;
use App\Http\Resources\ExtraGroupResource;
use App\Services\BackUpService\ModuleService;
use Illuminate\Http\JsonResponse;

class ModuleController extends AdminBaseController
{
    private ModuleService $service;

    public function __construct(ModuleService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Handle the incoming request.
     *
     * @param BookingRequest $request
     * @return JsonResponse
     */
    public function booking(BookingRequest $request): JsonResponse
    {
        $result = $this->service->unZip($request->file('file'));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            data_get($result, 'data')
        );
    }

}
