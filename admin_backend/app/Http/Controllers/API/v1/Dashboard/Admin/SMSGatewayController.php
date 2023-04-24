<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\SMSGatewayResource;
use App\Models\SmsGateway;
use App\Services\SMSGatewayService\SmsGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SMSGatewayController extends AdminBaseController
{
    private SmsGatewayService $service;

    public function __construct(SmsGatewayService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $gateways = SmsGateway::get();

        return SMSGatewayResource::collection($gateways);
    }


    /**
     * Display the specified resource.
     *
     * @param SmsGateway $smsGateway
     * @return JsonResponse
     */
    public function show(SmsGateway $smsGateway): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SMSGatewayResource::make($smsGateway)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SmsGateway $smsGateway
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function update(SmsGateway $smsGateway, FilterParamsRequest $request): JsonResponse
    {
        $result = $this->service->update($smsGateway, [
            'from'          => $request->input('from'),
            'api_key'       => $request->input('api_key'),
            'secret_key'    => $request->input('secret_key'),
            'service_id'    => $request->input('service_id'),
            'text'          => $request->input('text'),
        ]);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            SMSGatewayResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Set Model Active.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function setActive(int $id): JsonResponse
    {
        $result = $this->service->setActive($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            SMSGatewayResource::make(data_get($result, 'data'))
        );
    }

    public function dropAll(): JsonResponse
    {
        $this->service->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function truncate(): JsonResponse
    {
        $this->service->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function restoreAll(): JsonResponse
    {
        $this->service->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
