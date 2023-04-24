<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\Banner\AdminRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Repositories\BannerRepository\BannerRepository;
use App\Services\BannerService\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends AdminBaseController
{
    private BannerRepository $repository;
    private BannerService $service;

    public function __construct(BannerRepository $repository, BannerService $service)
    {
        parent::__construct();
        $this->repository   = $repository;
        $this->service      = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $filter = $request->merge(['type' => 'banner'])->all();

        $banners = $this->repository->bannersPaginate($filter);

        return BannerResource::collection($banners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (empty(data_get($validated, 'type'))) {
            $validated['type'] = 'banner';
        }

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            BannerResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Banner $banner
     * @return JsonResponse
     */
    public function show(Banner $banner): JsonResponse
    {
        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BannerResource::make(
                $banner->load([
                    'galleries',
                    'translations',
                    'translation' => fn($q) => $q->where('locale', $this->language),
                    'shops.translation' => fn($q) => $q->where('locale', $this->language),
                ])
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Banner $banner
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function update(Banner $banner, AdminRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (empty(data_get($validated, 'type'))) {
            $validated['type'] = 'banner';
        }

        $result = $this->service->update($banner, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            BannerResource::make(data_get($result, 'data'))
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
        $this->service->destroy($request->input('ids', []));

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

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function setActiveBanner(int $id): JsonResponse
    {
        $result = $this->service->setActiveBanner($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            BannerResource::make(data_get($result, 'data'))
        );
    }
}
