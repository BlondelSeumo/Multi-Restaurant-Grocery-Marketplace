<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Repositories\BannerRepository\BannerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends RestBaseController
{
    private BannerRepository $repository;

    /**
     * @param BannerRepository $repository
     */
    public function __construct(BannerRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $banners = $this->repository->bannersPaginate($request->merge(['active' => 1])->all());

        return BannerResource::collection($banners);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $banner = $this->repository->bannerDetails($id);

        if (empty($banner)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BannerResource::make($banner)
        );
    }

    public function likedBanner(int $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (empty($banner)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $banner->liked();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
        );
    }
}
