<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller\Booking;

use App\Helpers\ResponseError;
use App\Http\Controllers\API\v1\Dashboard\Seller\SellerBaseController;
use App\Http\Requests\Booking\Booking\SellerStoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Models\Booking\Booking;
use App\Repositories\Booking\BookingRepository\BookingRepository;
use App\Services\Booking\BookingService\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends SellerBaseController
{
    /**
     * @param BookingService $service
     * @param BookingRepository $repository
     */
    public function __construct(private BookingService $service, private BookingRepository $repository)
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

        return BookingResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SellerStoreRequest $request
     * @return JsonResponse
     */
    public function store(SellerStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            BookingResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param Booking $booking
     * @return JsonResponse
     */
    public function show(Booking $booking): JsonResponse
    {
        if ($booking->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BookingResource::make($booking)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Booking $booking
     * @param SellerStoreRequest $request
     * @return JsonResponse
     */
    public function update(Booking $booking, SellerStoreRequest $request): JsonResponse
    {
        if ($booking->shop_id !== $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->service->update($booking, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            BookingResource::make(data_get($result, 'data'))
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
                'code'      => data_get($result, 'code'),
                'message'   => __('errors.' . ResponseError::ERROR_505, ['ids' => data_get($result, 'message')], $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
