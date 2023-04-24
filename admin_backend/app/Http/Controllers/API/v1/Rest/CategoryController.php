<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\CategoryFilterRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepoInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends RestBaseController
{
    public function __construct(private CategoryRepoInterface $repository)
    {
        parent::__construct();
    }

    public function parentCategory(CategoryFilterRequest $request): JsonResponse
    {
        $categories = $this->repository->parentCategories($request->merge(['active' => 1])->all());

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            CategoryResource::collection($categories)
        );
    }

    public function childrenCategory(int $id): JsonResponse
    {
        $childrenCategories = $this->repository->childrenCategory($id);

        if (!$childrenCategories) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            CategoryResource::make($childrenCategories)
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */

    public function paginate(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $categories = $this->repository->parentCategories($request->merge(['active' => 1])->all());

        return CategoryResource::collection($categories);
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */

    public function selectPaginate(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $categories = $this->repository->selectPaginate($request->merge(['active' => 1])->all());

        return CategoryResource::collection($categories);
    }

    /**
     * Search Model by tag name.
     *
     * @param CategoryFilterRequest $request
     * @return JsonResponse
     */
    public function categoriesSearch(CategoryFilterRequest $request): JsonResponse
    {
        $categories = $this->repository->categoriesSearch($request->merge(['active' => 1])->all());

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            CategoryResource::collection($categories)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $category = $this->repository->categoryByUuid($uuid);

        if (!$category) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            CategoryResource::make($category)
        );
    }

    public function shopCategoryProduct(Request $request): AnonymousResourceCollection
    {
        $categories = $this->repository->shopCategoryProduct($request->all());

        return CategoryResource::collection($categories);
    }

    public function shopCategory(Request $request): AnonymousResourceCollection
    {
        $categories = $this->repository->shopCategory($request->all());

        return CategoryResource::collection($categories);
    }

    public function types(): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
            array_keys(Category::TYPES)
        );
    }
}
