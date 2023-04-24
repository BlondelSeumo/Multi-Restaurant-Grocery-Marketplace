<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\ExtraGroup\StoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\ExtraGroupResource;
use App\Models\ExtraGroup;
use App\Repositories\ExtraRepository\ExtraGroupRepository;
use App\Services\ExtraGroupService\ExtraGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExtraGroupController extends AdminBaseController
{

    private ExtraGroupRepository $repository;
    private ExtraGroupService $service;

    public function __construct(ExtraGroupService $service, ExtraGroupRepository $repository)
    {
        parent::__construct();
        $this->service    = $service;
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
        $extras = $this->repository->extraGroupList($request->all());

        return ExtraGroupResource::collection($extras);
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
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ExtraGroupResource::make(data_get($result, 'data'))
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
        $extra = $this->repository->extraGroupDetails($id);

        if (!$extra) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ExtraGroupResource::make($extra->loadMissing([
                'translations',
                'extraValues.group.translation' => fn($q) => $q->where('locale', $this->language),
            ]))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id, StoreRequest $request): JsonResponse
    {
        $result = $this->service->update($id, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ExtraGroupResource::make(data_get($result, 'data'))
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
        $hasValues = $this->service->delete($request->input('ids'));

        if ($hasValues > 0) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_504]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * ExtraGroup type list.
     *
     * @return JsonResponse
     */
    public function typesList(): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ExtraGroup::TYPES
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
            ExtraGroupResource::make(data_get($result, 'data'))
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
