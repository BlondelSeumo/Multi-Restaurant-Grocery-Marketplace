<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\EmailSetting\StoreRequest;
use App\Http\Resources\EmailSettingResource;
use App\Models\EmailSetting;
use App\Repositories\EmailSettingRepository\EmailSettingRepository;
use App\Services\EmailSettingService\EmailSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class EmailSettingController extends AdminBaseController
{
    private EmailSettingService $service;
    private EmailSettingRepository $repository;

    /**
     * @param EmailSettingService $service
     * @param EmailSettingRepository $repository
     */
    public function __construct(EmailSettingService $service, EmailSettingRepository $repository)
    {
        parent::__construct();

        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $emailSettings = $this->repository->get($request->all());

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {

            $ips = collect(Cache::get('block-ips'));

            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException) {}

            abort(403);
        }

        return EmailSettingResource::collection($emailSettings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $exist = EmailSetting::where('host', data_get($data, 'host'))->exists();

        if ($exist) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_209]);
        }

        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param EmailSetting $emailSetting
     * @return JsonResponse
     */
    public function show(EmailSetting $emailSetting): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            EmailSettingResource::make($this->repository->show($emailSetting))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmailSetting $emailSetting
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(EmailSetting $emailSetting, StoreRequest $request): JsonResponse
    {
        $result = $this->service->update($emailSetting, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $this->service->delete($request->input('ids', []));

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function setActive(int $id): JsonResponse
    {
        $this->service->setActive($id);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function dropAll(): JsonResponse
    {
        $this->service->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->service->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->service->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
