<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Exports\CategoryExport;
use App\Helpers\ResponseError;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryFilterRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\CategoryResource;
use App\Imports\CategoryImport;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepoInterface;
use App\Services\CategoryServices\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CategoryController extends SellerBaseController
{

    private CategoryService $categoryService;
    private CategoryRepoInterface $categoryRepository;

    public function __construct(CategoryService $categoryService, CategoryRepoInterface $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param CategoryFilterRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(CategoryFilterRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $categories = $this->categoryRepository->parentCategories($request->all());

        return CategoryResource::collection($categories);
    }

    /**
     * Display a listing of the resource with paginate.
     *
     * @param CategoryFilterRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function paginate(CategoryFilterRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $categories = $this->categoryRepository->parentCategories($request->all());

        return CategoryResource::collection($categories);
    }

    /**
     * Display a listing of the resource with paginate.
     *
     * @param CategoryFilterRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function selectPaginate(CategoryFilterRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $categories = $this->categoryRepository->selectPaginate($request->except(['active']));

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryCreateRequest $request
     * @return JsonResponse
     */
    public function store(CategoryCreateRequest $request): JsonResponse
    {
        $result = $this->categoryService->create($request->validated());

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
     * @param  string  $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $category = $this->categoryRepository->categoryByUuid($uuid);

        if (!$category) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $category->load([
            'translations',
            'metaTags',
        ]);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CategoryResource::make($category)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryCreateRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(string $uuid, CategoryCreateRequest $request): JsonResponse
    {
        $result = $this->categoryService->update($uuid, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(__('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED));
    }

    /**
     * Remove Model image from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function imageDelete(string $uuid): JsonResponse
    {
        $category = Category::firstWhere('uuid', $uuid);

        if (!$category) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $category->galleries()->where('path', $category->img)->delete();

        $category->update(['img' => null]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language),
            $category
        );
    }

    /**
     * Search Model by tag name.
     *
     * @param CategoryFilterRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function categoriesSearch(CategoryFilterRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $categories = $this->categoryRepository->categoriesSearch($request->all());

        return CategoryResource::collection($categories);
    }

    /**
     * Change Active Status of Model.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function setActive(string $uuid): JsonResponse
    {
        $category = $this->categoryRepository->categoryByUuid($uuid);

        if (!$category) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $category->update(['active' => !$category->active]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            CategoryResource::make($category)
        );
    }

    public function fileExport(): JsonResponse
    {
        $fileName = 'export/categories.xls';

        try {
            Excel::store(new CategoryExport($this->language), $fileName, 'public');
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse('Error during export');
        }

        return $this->successResponse('Successfully exported', [
            'path'      => 'public/export',
            'file_name' => $fileName
        ]);
    }

    public function fileImport(Request $request): JsonResponse
    {
        try {
            Excel::import(new CategoryImport($this->language), $request->file('file'));

            return $this->successResponse('Successfully imported');
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse(
                ResponseError::ERROR_508,
                'Excel format incorrect or data invalid'
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->categoryService->delete($request->input('ids', []));

        if (!empty(data_get($result, 'data'))) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_504,
                'message'   => 'Can`t delete record that has children or products.'
            ]);
        }

        return $this->successResponse(__('web.record_has_been_successfully_delete'));
    }
}
