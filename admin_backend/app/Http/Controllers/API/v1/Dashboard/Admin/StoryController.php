<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Repositories\StoryRepository\StoryRepository;
use App\Services\StoryService\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;

class StoryController extends AdminBaseController
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

        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        Artisan::call('remove:expired:stories');

        $story = $this->repository->index($request->all());

        return StoryResource::collection($story);
    }

    /**
     * Display the specified resource.
     *
     * @param Story $story
     * @return JsonResponse
     */
    public function show(Story $story): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            StoryResource::make($this->repository->show($story)),
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
        $this->service->delete($request->input('ids', []));

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function dropAll(): JsonResponse
    {
        $this->service->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function truncate(): JsonResponse
    {
        $this->service->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function restoreAll(): JsonResponse
    {
        $this->service->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

}
