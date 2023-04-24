<?php

namespace App\Http\Controllers\API\v1\Dashboard\User\Booking;

use App\Helpers\ResponseError;
use App\Http\Controllers\API\v1\Dashboard\User\UserBaseController;
use App\Http\Requests\Booking\Booking\UserBookingStoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Booking\UserBookingResource;
use App\Models\Booking\UserBooking;
use App\Repositories\Booking\BookingRepository\UserBookingRepository;
use App\Services\Booking\BookingService\UserBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserBookingController extends UserBaseController
{
    /**
     * @param UserBookingService $service
     * @param UserBookingRepository $repository
     */
    public function __construct(private UserBookingService $service, private UserBookingRepository $repository)
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
        $model = $this->repository->paginate($request->merge(['user_id' => auth('sanctum')->id()])->all());

        return UserBookingResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserBookingStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserBookingStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth('sanctum')->id();

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            UserBookingResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param UserBooking $booking
     * @return JsonResponse
     */
    public function show(UserBooking $booking): JsonResponse
    {
        if ($booking->user_id !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->repository->show($booking);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UserBookingResource::make($result)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserBooking $booking
     * @param UserBookingStoreRequest $request
     * @return JsonResponse
     */
    public function update(UserBooking $booking, UserBookingStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth('sanctum')->id();

        if ($booking->user_id !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->service->update($booking, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            UserBookingResource::make(data_get($result, 'data'))
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
        $result = $this->service->delete($request->input('ids', []), auth('sanctum')->id());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
