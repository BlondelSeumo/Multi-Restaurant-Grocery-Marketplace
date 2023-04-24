<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\TermCondition\StoreRequest;
use App\Models\TermCondition;
use App\Services\TermService\TermService;
use Illuminate\Http\JsonResponse;

class TermsController extends AdminBaseController
{
    private TermService $service;

    /**
     * @param TermService $service
     */
    public function __construct(TermService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $exist = TermCondition::withTrashed()->first();

        $exist?->forceDelete();

        $termCondition = $this->service->create($request->validated());

        if (!data_get($termCondition, 'status')) {
            return $this->onErrorResponse($termCondition);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($termCondition, 'data')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $termCondition = TermCondition::with('translations')->first();

        if (empty($termCondition)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $termCondition
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
