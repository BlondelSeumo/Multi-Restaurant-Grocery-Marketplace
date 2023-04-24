<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\Review\PaginateRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Repositories\ReviewRepository\ReviewRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends SellerBaseController
{
    /**
     * @param ReviewRepository $repository
     */
    public function __construct(private ReviewRepository $repository)
    {
        parent::__construct();
    }

    /**
     * @param PaginateRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(PaginateRequest $request): AnonymousResourceCollection
    {
        $filter = $request->merge(['assign' => 'shop', 'assign_id' => $this->shop->id])->all();

        return ReviewResource::collection($this->repository->paginate($filter));
    }

    /**
     * @param Review $review
     * @return JsonResponse
     */
    public function show(Review $review): JsonResponse
    {
        $review = $this->repository->show($review);

        if ($review->assignable_id !== $this->shop->id || $review->reviewable?->shop?->id !== $this->shop->id) {
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
}
