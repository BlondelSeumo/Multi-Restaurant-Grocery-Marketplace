<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Review\PaginateRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Repositories\ReviewRepository\ReviewRepository;
use App\Services\ReviewService\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends AdminBaseController
{
    private ReviewService $service;
    private ReviewRepository $repository;

    /**
     * @param ReviewService $service
     * @param ReviewRepository $repository
     */
    public function __construct(ReviewService $service, ReviewRepository $repository)
    {
        parent::__construct();
        $this->service      = $service;
        $this->repository   = $repository;
    }

    /**
     * @param PaginateRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(PaginateRequest $request): AnonymousResourceCollection
    {
        return ReviewResource::collection($this->repository->paginate($request->all()));
    }

    /**
     * @param Review $review
     * @return JsonResponse
     */
    public function show(Review $review): JsonResponse
    {
        $review = $this->repository->show($review);

        if (empty($review)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ReviewResource::make($review)
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
        $this->service->destroy(is_array($request->input('ids')) ? $request->input('ids') : []);

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
