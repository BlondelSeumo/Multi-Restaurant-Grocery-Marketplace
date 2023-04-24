<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\CategoryFilterRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\ShopCategory\StoreRequest;
use App\Http\Requests\ShopCategory\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository\CategoryRepository;
use App\Services\ShopCategoryService\ShopCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopCategoryController extends SellerBaseController
{
    private CategoryRepository $categoryRepository;
    private ShopCategoryService $shopCategoryService;

    public function __construct(ShopCategoryService $shopCategoryService, CategoryRepository $categoryRepository)
    {
        parent::__construct();

        $this->shopCategoryService = $shopCategoryService;
        $this->categoryRepository  = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $shopBrands = $this->categoryRepository->shopCategory($request->merge(['shop_id' => $this->shop->id])->all());

        return CategoryResource::collection($shopBrands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->shopCategoryService->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
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
        $model = $this->categoryRepository->shopCategoryById($id, $this->shop->id);

        if (!$model) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CategoryResource::make($model)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $collection = $request->validated();
        $collection['shop_id'] = $this->shop->id;

        $result = $this->shopCategoryService->update($collection, $this->shop);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            data_get($result, 'data')
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
        $this->shopCategoryService->delete($request->input('ids', []), $this->shop->id);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */
    public function allCategory(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $category = $this->categoryRepository->shopCategoryNonExistPaginate(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

        return CategoryResource::collection($category);
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $shopBrands = $this->categoryRepository->shopCategoryPaginate(
            $request->merge(['shop_id' => $this->shop->id])->all()
        );

        return CategoryResource::collection($shopBrands);
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return AnonymousResourceCollection
     */
    public function selectPaginate(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $shopBrands = $this->categoryRepository->selectPaginate(
            $request->merge(['shop_id' => $this->shop->id, 'type' => Category::MAIN])->all()
        );

        return CategoryResource::collection($shopBrands);
    }
}
