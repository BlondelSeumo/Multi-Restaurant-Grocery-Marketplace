<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\PrivacyPolicy\StoreRequest;
use App\Models\PrivacyPolicy;
use App\Services\PrivacyPolicyService\PrivacyPolicyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class PrivacyPolicyController extends AdminBaseController
{
    private PrivacyPolicy $model;
    private PrivacyPolicyService $service;

    /**
     * @param PrivacyPolicy $model
     * @param PrivacyPolicyService $service
     */
    public function __construct(PrivacyPolicy $model,PrivacyPolicyService $service)
    {
        parent::__construct();
        $this->model    = $model;
        $this->service  = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {

            $ips = collect(Cache::get('block-ips'));

            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException $e) {
            }

            abort(403);
        }

        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $model = $this->model->with([
            'translation' => fn($q) => $q->where('locale', $this->language),
            'translations'
        ])->first();

        if (empty($model)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $model);
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
