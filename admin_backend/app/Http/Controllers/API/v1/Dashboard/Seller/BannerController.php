<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\Banner\SellerRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Repositories\BannerRepository\BannerRepository;
use App\Services\BannerService\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends SellerBaseController
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
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $filter = $request->merge(['type' => 'banner', 'shops' => [$this->shop->id]])->all();

        $banners = $this->repository->bannersPaginate($filter);

        return BannerResource::collection($banners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SellerRequest $request
     * @return JsonResponse
     */
    public function store(SellerRequest $request): JsonResponse
    {
        $validated          = $request->validated();
        $validated['shops'] = [$this->shop->id];

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
        if (!in_array($this->shop->id, $banner->shops)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BannerResource::make($banner->loadMissing([
                'galleries',
                'translations',
                'translation' => fn($q) => $q->where('locale', $this->language),
                'shops.translation' => fn($q) => $q->where('locale', $this->language),
            ]))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Banner $banner
     * @param SellerRequest $request
     * @return JsonResponse
     */
    public function update(Banner $banner, SellerRequest $request): JsonResponse
    {
        if (!in_array($this->shop->id, $banner->shops)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $validated          = $request->validated();
        $validated['shops'] = [$this->shop->id];

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
        $this->service->destroy($request->input('ids', []), $this->shop->id);

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
        $banner = Banner::find($id);

        $existShop = $banner->shops->where('id', $this->shop->id)->first();

        if (!empty($banner) && !empty($existShop)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_101,
                'message' => __('errors.' . ResponseError::ERROR_101, locale: $this->language)
            ]);
        }

        $banner->update(['active' => !$banner->active]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            BannerResource::make($banner)
        );
    }
}
