<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Exports\BrandExport;
use App\Helpers\ResponseError;
use App\Http\Requests\BrandCreateRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\BrandResource;
use App\Imports\BrandImport;
use App\Models\Brand;
use App\Repositories\BrandRepository\BrandRepository;
use App\Services\Interfaces\BrandServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BrandController extends AdminBaseController
{
    private BrandRepository $brandRepository;
    private BrandServiceInterface $brandService;

    /**
     * @param BrandRepository $brandRepository
     * @param BrandServiceInterface $brandService
     */
    public function __construct(BrandRepository $brandRepository, BrandServiceInterface $brandService)
    {
        parent::__construct();
        $this->brandRepository = $brandRepository;
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $brands = $this->brandRepository->brandsList($request->all());

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BrandResource::collection($brands)
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $brands = $this->brandRepository->brandsPaginate($request->all());

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BrandCreateRequest $request
     * @return JsonResponse
     */
    public function store(BrandCreateRequest $request): JsonResponse
    {
        $result = $this->brandService->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            BrandResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BrandResource::make($brand->loadMissing(['metaTags']))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Brand $brand
     * @param BrandCreateRequest $request
     * @return JsonResponse
     */
    public function update(Brand $brand, BrandCreateRequest $request): JsonResponse
    {
        $result = $this->brandService->update($brand, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED),
            BrandResource::make(data_get($result, 'data'))
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
        $result = $this->brandService->delete($request->input('ids', []));

        if (!empty(data_get($result, 'data'))) {
            $code = data_get($result, 'code');
            return $this->onErrorResponse([
                'code'    => $code,
                'message' => __("errors.$code", locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function dropAll(): JsonResponse
    {
        $this->brandService->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->brandService->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->brandService->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * Search Model by tag name.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function brandsSearch(Request $request): AnonymousResourceCollection
    {
        $brands = $this->brandRepository->brandsSearch($request->input('search', ''));

        return BrandResource::collection($brands);
    }

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @return BrandResource
     */
    public function setActive(int $id): BrandResource
    {
        /** @var Brand $brand */
        $brand = $this->brandRepository->brandDetails($id);

        $brand->update([
            'active' => !$brand->active
        ]);

        return BrandResource::make($brand);
    }

    public function fileExport(): JsonResponse
    {
        $fileName = 'export/brands.xls';

        try {
            Excel::store(new BrandExport, $fileName, 'public');

            return $this->successResponse('Successfully exported', [
                'path' => 'public/export',
                'file_name' => $fileName
            ]);
        } catch (Throwable $e) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_400,
                'message' => "Error during export" . $e->getMessage()
            ]);
        }
    }

    public function fileImport(Request $request): JsonResponse
    {
        try {
            Excel::import(new BrandImport, $request->file('file'));

            return $this->successResponse('Successfully imported');
        } catch (Exception $e) {
            return $this->errorResponse(
                ResponseError::ERROR_508,
                'Excel format incorrect or data invalid ' . $e->getMessage()
            );
        }
    }
}
