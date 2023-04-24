<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\ExtraValue\StoreRequest;
use App\Http\Requests\ExtraValue\UpdateRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\ExtraValueResource;
use App\Repositories\ExtraRepository\ExtraValueRepository;
use App\Services\ExtraValueService\ExtraValueService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExtraValueController extends AdminBaseController
{

    private ExtraValueRepository $repository;
    private ExtraValueService $service;

    /**
     * @param ExtraValueService $service
     * @param ExtraValueRepository $repository
     */
    public function __construct(ExtraValueService $service, ExtraValueRepository $repository)
    {
        parent::__construct();
        $this->service      = $service;
        $this->repository   = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $values = $this->repository->extraValueList(
            $request->input('active'),
            $request->input('group_id')
        );

        return ExtraValueResource::collection($values);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ExtraValueResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $extraValue = $this->repository->extraValueDetails($id);

        if (!$extraValue) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ExtraValueResource::make($extraValue)
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(int $id, UpdateRequest $request): JsonResponse
    {
        $result = $this->service->update($id, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ExtraValueResource::make(data_get($result, 'data'))
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
     * Change Active Status of Model.
     *
     * @param int $id
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
            ExtraValueResource::make(data_get($result, 'data'))
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
