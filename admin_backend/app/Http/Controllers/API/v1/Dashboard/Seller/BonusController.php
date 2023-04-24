<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\Bonus\StoreRequest;
use App\Http\Requests\Bonus\UpdateRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Bonus\BonusResource;
use App\Models\Bonus;
use App\Models\Shop;
use App\Models\Stock;
use App\Repositories\BonusRepository\BonusRepository;
use App\Services\BonusService\BonusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BonusController extends SellerBaseController
{
    private BonusService $service;
    private BonusRepository $repository;

    /**
     * @param BonusService $service
     * @param BonusRepository $repository
     */
    public function __construct(BonusService $service, BonusRepository $repository)
    {
        parent::__construct();

        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $bonus = $this->repository->paginate($request->merge(['shop_id' => $this->shop->id])->all());

        return BonusResource::collection($bonus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $collection = $request->validated();

        $type = data_get($collection, 'type');

        $collection['shop_id']          = $this->shop->id;
        $collection['bonusable_type']   = $type === Bonus::TYPE_SUM ? Shop::class : Stock::class;

        $result = $this->service->create($collection);

        if (!data_get($result, 'status')) {
          return  $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Bonus $bonus
     * @return JsonResponse
     */
    public function show(Bonus $bonus): JsonResponse
    {
        if (data_get($bonus, 'shop_id') !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $shopBonus = $this->repository->show($bonus);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BonusResource::make($shopBonus)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Bonus $bonus
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Bonus $bonus, UpdateRequest $request): JsonResponse
    {
        if (data_get($bonus, 'shop_id') !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $collection = $request->validated();

        $type = data_get($collection, 'type');

        $collection['shop_id']          = $this->shop->id;
        $collection['bonusable_type']   = $type === Bonus::TYPE_SUM ? Shop::class : Stock::class;

        $result = $this->service->update($bonus, $collection);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
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

    public function statusChange(int $id): JsonResponse
    {
        $bonus = Bonus::find($id);

        if (data_get($bonus, 'shop_id') !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->service->statusChange($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            BonusResource::make(data_get($result, 'data'))
        );
    }

}
