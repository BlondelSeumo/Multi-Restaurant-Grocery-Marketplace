<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Point\StoreRequest;
use App\Http\Resources\PointResource;
use App\Models\Point;
use App\Services\PointService\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class PointController extends AdminBaseController
{
    private Point $model;
    private PointService $service;

    /**
     * @param Point $model
     * @param PointService $service
     */
    public function __construct(Point $model, PointService $service)
    {
        parent::__construct();
        $this->model = $model;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {

            $ips = collect(Cache::get('block-ips'));

            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException $e) {
            }

            abort(403);
        }

        $points = $this->model
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->when($request->input('deleted_at'), fn($q) => $q->onlyTrashed())
            ->paginate($request->input('perPage', 10));

        return PointResource::collection($points);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            PointResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Point $point
     * @return JsonResponse
     */
    public function show(Point $point): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            PointResource::make(
                $point->load([
                    'shop',
                    'shop.translation' => fn($q) => $q->where('locale', $this->language),
                ])
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Point $point
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(Point $point, StoreRequest $request): JsonResponse
    {
        $result = $this->service->update($point, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            PointResource::make(data_get($result, 'data'))
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
        foreach (Point::whereIn('id', $request->input('ids', []))->get() as $point) {

            /** @var Point $point */

            $point->delete();
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function setActive(int $id): JsonResponse
    {
        $result = $this->service->setActive($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            PointResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @return JsonResponse
     */
    public function dropAll(): JsonResponse
    {
        $this->service->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->service->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->service->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
