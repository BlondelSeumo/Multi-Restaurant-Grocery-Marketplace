<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Receipt\ShopStoreRequest;
use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use App\Repositories\ReceiptRepository\ReceiptRepository;
use App\Services\ReceiptService\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReceiptController extends SellerBaseController
{
    public function __construct(private ReceiptService $service, private ReceiptRepository $repository)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $model = $this->repository->paginate($request->merge(['shop_id' => $this->shop->id])->all());

        return ReceiptResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShopStoreRequest $request
     * @return JsonResponse
     */
    public function store(ShopStoreRequest $request): JsonResponse
    {
        $validated              = $request->validated();
        $validated['shop_id']   = $this->shop->id;

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            ReceiptResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param Receipt $receipt
     * @return JsonResponse
     */
    public function show(Receipt $receipt): JsonResponse
    {
        if ($receipt->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ReceiptResource::make($this->repository->show($receipt))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Receipt $receipt
     * @param ShopStoreRequest $request
     * @return JsonResponse
     */
    public function update(Receipt $receipt, ShopStoreRequest $request): JsonResponse
    {
        if ($receipt->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $validated              = $request->validated();
        $validated['shop_id']   = $this->shop->id;

        $result = $this->service->update($receipt, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            ReceiptResource::make(data_get($result, 'data'))
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
        $result = $this->service->delete($request->input('ids', []), $this->shop->id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
