<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FaqSetRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\FAQResource;
use App\Models\Faq;
use App\Services\FaqService\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class FAQController extends AdminBaseController
{
    private Faq $model;
    private FaqService $service;

    public function __construct(Faq $model, FaqService $service)
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
        $faqs = $this->model->with([
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])
            ->orderBy($request->input('column','id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 15));

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return FAQResource::collection($faqs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FaqSetRequest $request
     * @return JsonResponse
     */
    public function store(FaqSetRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            FAQResource::make(data_get($result, 'data'))
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
        $faq = $this->model->with([
            'translations',
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])->firstWhere('uuid', $uuid);

        if (empty($faq)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            FAQResource::make($faq)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FaqSetRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(string $uuid, FaqSetRequest $request): JsonResponse
    {
        $result = $this->service->update($uuid, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            FAQResource::make(data_get($result, 'data'))
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
        foreach (Faq::whereIn('uuid', $request->input('ids', []))->get() as $faq) {
            $faq->delete();
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function setActiveStatus(string $uuid): JsonResponse
    {
        $result = $this->service->setStatus($uuid);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language),
            FAQResource::make(data_get($result, 'data'))
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
