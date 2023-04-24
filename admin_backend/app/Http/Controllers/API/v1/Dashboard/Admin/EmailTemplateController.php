<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\EmailSetting\EmailTemplateRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository\EmailTemplateRepository;
use App\Services\EmailTemplateService\EmailTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class EmailTemplateController extends AdminBaseController
{
    private EmailTemplateService $service;
    private EmailTemplateRepository $repository;

    /**
     * @param EmailTemplateService $service
     * @param EmailTemplateRepository $repository
     */
    public function __construct(EmailTemplateService $service, EmailTemplateRepository $repository)
    {
        parent::__construct();
        $this->service      = $service;
        $this->repository   = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {

            $ips = collect(Cache::get('block-ips'));

            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException) {
            }

            abort(403);
        }

        return EmailTemplateResource::collection($this->repository->paginate($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmailTemplateRequest $request
     * @return JsonResponse
     */
    public function store(EmailTemplateRequest $request): JsonResponse
    {
        $result = $this->service->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param EmailTemplate $emailTemplate
     * @return JsonResponse
     */
    public function show(EmailTemplate $emailTemplate): JsonResponse
    {
        $show = $this->repository->show($emailTemplate);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            EmailTemplateResource::make($show)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmailTemplate $emailTemplate
     * @param EmailTemplateRequest $request
     * @return JsonResponse
     */
    public function update(EmailTemplate $emailTemplate, EmailTemplateRequest $request): JsonResponse
    {
        $result = $this->service->update($emailTemplate, $request->validated());

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
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language), EmailTemplate::TYPES
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
