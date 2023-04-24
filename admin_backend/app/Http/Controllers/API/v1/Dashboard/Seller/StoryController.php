<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Story\StoreRequest;
use App\Http\Requests\Story\UpdateRequest;
use App\Http\Requests\Story\UploadFileRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Repositories\StoryRepository\StoryRepository;
use App\Services\StoryService\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;

class StoryController extends SellerBaseController
{
    private StoryService $service;
    private StoryRepository $repository;

    /**
     * @param StoryService $service
     * @param StoryRepository $repository
     */
    public function __construct(StoryService $service, StoryRepository $repository)
    {
        parent::__construct();

        $this->service      = $service;
        $this->repository   = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        Artisan::call('remove:expired:stories');

        $story = $this->repository->index($request->merge(['shop_id' => $this->shop->id])->all());

        return StoryResource::collection($story);
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

        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(__('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language));
    }

    /**
     * Display the specified resource.
     *
     * @param Story $story
     * @return JsonResponse
     */
    public function show(Story $story): JsonResponse
    {
        if (empty($story) || $story->updated_at >= date('Y-m-d', strtotime('+1 day'))) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        if ($story->shop_id != $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language), StoryResource::make($this->repository->show($story)),
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Story $story
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Story $story, UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        if ($story->shop_id != $this->shop->id) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $result = $this->service->update($story, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(__('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UploadFileRequest $request
     * @return JsonResponse
     */
    public function uploadFiles(UploadFileRequest $request): JsonResponse
    {
        $result = $this->service->uploadFiles($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
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
        $this->service->delete($request->input('ids', []), $this->shop->id);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
