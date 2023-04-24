<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Exports\ProductExport;
use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Order\OrderChartRequest;
use App\Http\Requests\Product\addAddonInStockRequest;
use App\Http\Requests\Product\addInStockRequest;
use App\Http\Requests\Product\AdminRequest;
use App\Http\Requests\Product\StatusRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\UserActivityResource;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Repositories\Interfaces\ProductRepoInterface;
use App\Services\ProductService\ProductAdditionalService;
use App\Services\ProductService\ProductService;
use App\Services\ProductService\StockService;
use App\Traits\Loggable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProductController extends AdminBaseController
{
    use Loggable;

    private ProductService $productService;
    private ProductRepoInterface $productRepository;

    /**
     * @param ProductService $productService
     * @param ProductRepoInterface $productRepository
     */
    public function __construct(ProductService $productService, ProductRepoInterface $productRepository)
    {
        parent::__construct();
        $this->productService       = $productService;
        $this->productRepository    = $productRepository;
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->productsPaginate($request->all());

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $result = $this->productService->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ProductResource::make(data_get($result, 'data'))
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
        $product = $this->productRepository->productByUUID($uuid);

        if (empty($product)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ProductResource::make($product->loadMissing(['translations', 'metaTags']))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdminRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(AdminRequest $request, string $uuid): JsonResponse
    {
        $result = $this->productService->update($uuid, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ProductResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function selectStockPaginate(FilterParamsRequest $request): JsonResponse
    {
        $shop = Shop::find((int)$request->input('shop_id'));

        if (!$shop?->id) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_400]);
        }

        $stocks = $this->productRepository->selectStockPaginate(
            $request->merge(['shop_id' => $shop->id])->all()
        );

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            StockResource::collection($stocks)
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
        $result = $this->productService->delete($request->input('ids', []));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
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
        $this->productService->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->productService->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->productService->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function dropAllStocks(): JsonResponse
    {
        (new StockService)->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncateStocks(): JsonResponse
    {
        (new StockService)->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAllStocks(): JsonResponse
    {
        (new StockService)->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * Add Product Properties.
     *
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     */
    public function addProductProperties(string $uuid, Request $request): JsonResponse
    {
        $result = (new ProductAdditionalService)->createOrUpdateProperties($uuid, $request->all());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ProductResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Add Product Properties.
     *
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     */
    public function addProductExtras(string $uuid, Request $request): JsonResponse
    {
        $result = (new ProductAdditionalService)->createOrUpdateExtras($uuid, $request->all());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ProductResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Add Product Properties.
     *
     * @param string $uuid
     * @param addInStockRequest $request
     * @return JsonResponse
     */
    public function addInStock(string $uuid, addInStockRequest $request): JsonResponse
    {
        $product = Product::firstWhere('uuid', $uuid);

        if (!$product) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $product->addInStock($request->validated());

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ProductResource::make($product->load([
                'translation' => fn($q) => $q->where('locale', $this->language),
                'stocks.addons',
                'stocks.addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
            ]))
        );
    }

    /**
     * Add Product Properties.
     *
     * @param int $id
     * @param addAddonInStockRequest $request
     * @return JsonResponse
     */
    public function addAddonInStock(int $id, addAddonInStockRequest $request): JsonResponse
    {
        $stock = Stock::firstWhere('id', $id);

        if (empty($stock)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => ResponseError::ERROR_404
            ]);
        }

        if ($stock->addon || $stock->countable?->addon) {
            $stock->addons()->delete();
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_400,
                'message'   => 'Stock or his product is addon'
            ]);
        }

        $result = $this->productService->syncAddons($stock, data_get($request->validated(), 'addons'));

        if (count($result) > 0) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_400,
                'message' => "Products or his stocks is not addon or other shop #" . implode(', #', $result)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            StockResource::make($stock->load([
                'addons.addon.translation' => fn($q) => $q->where('locale', $this->language),
                'countable.translation' => fn($q) => $q->where('locale', $this->language)
            ]))
        );
    }

    /**
     * Search Model by tag name.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function productsSearch(Request $request): AnonymousResourceCollection
    {
        $categories = $this->productRepository->productsSearch($request->all());

        return ProductResource::collection($categories);
    }

    /**
     * Change Active Status of Model.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function setActive(string $uuid): JsonResponse
    {
        $product = $this->productRepository->productByUUID($uuid);

        if (empty($product)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $product->update(['active' => !$product->active]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ProductResource::make($product)
        );
    }

    /**
     * Change Active Status of Model.
     *
     * @param string $uuid
     * @param StatusRequest $request
     * @return JsonResponse
     */
    public function setStatus(string $uuid, StatusRequest $request): JsonResponse
    {
        /** @var Product $product */
        $product = $this->productRepository->productByUUID($uuid);

        if (!$product) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        if ($product->stocks?->sum('quantity') === 0 && $request->input('status') === Product::PUBLISHED) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_430]);
        }

        $product->update([
            'status' => $request->input('status')
        ]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ProductResource::make($product)
        );
    }

    public function fileExport(Request $request): JsonResponse
    {
        $fileName = 'export/products.xls';

        $productExport = new ProductExport($request->merge(['language' => $this->language])->all());

        try {
            Excel::store($productExport, $fileName, 'public');

            return $this->successResponse('Successfully exported', [
                'path'      => 'public/export',
                'file_name' => $fileName
            ]);
        } catch (Throwable $e) {
            $this->error($e);
        }

        return $this->errorResponse('Error during export');
    }

    public function history(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $history = $this->productRepository->history($request->all());

        return UserActivityResource::collection($history);
    }

    public function fileImport(Request $request): JsonResponse
    {
        $shopId = $request->input('shop_id');

        try {
            Excel::import(new ProductImport($shopId, $this->language), $request->file('file'));

            return $this->successResponse('Successfully imported');
        } catch (Exception) {
            return $this->onErrorResponse([
                'code'  => ResponseError::ERROR_508,
                'data'  => 'Excel format incorrect or data invalid'
            ]);
        }
    }

    public function reportChart(OrderChartRequest $request): JsonResponse
    {
        try {
            $result = $this->productRepository->reportChart($request->all());

            return $this->successResponse('Successfully', $result);
        } catch (Exception $exception) {
            return $this->errorResponse(ResponseError::ERROR_400, $exception->getMessage());
        }
    }

    public function reportPaginate(FilterParamsRequest $request): JsonResponse
    {
        try {
            $result = $this->productRepository->productReportPaginate($request->all());

            return $this->successResponse(
                'Successfully',
                data_get($result, 'data')
            );
        } catch (Exception $exception) {
            return $this->errorResponse(ResponseError::ERROR_400, $exception->getMessage());
        }
    }

    public function stockReportPaginate(FilterParamsRequest $request): JsonResponse
    {
        try {
            $result = $this->productRepository->stockReportPaginate($request->all());

            return $this->successResponse('', $result);
        } catch (Exception $exception) {
            return $this->errorResponse(ResponseError::ERROR_400, $exception->getMessage());
        }
    }

    public function mostPopulars(FilterParamsRequest $request): LengthAwarePaginator
    {
        return $this->productRepository->mostPopulars($request->all());
    }

}
