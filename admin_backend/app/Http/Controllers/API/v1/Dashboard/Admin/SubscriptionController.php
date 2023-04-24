<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\Subscription\UpdateRequest;
use App\Http\Resources\EmailSubscriptionResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\EmailSubscription;
use App\Models\Subscription;
use App\Services\SubscriptionService\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class SubscriptionController extends AdminBaseController
{
    private Subscription $model;
    private SubscriptionService $subscriptionService;

    /**
     * @param Subscription $model
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(Subscription $model, SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->model = $model;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subscriptions = $this->model->subscriptionsList();

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SubscriptionResource::collection($subscriptions)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function store(UpdateRequest $request): JsonResponse
    {
        $result = $this->subscriptionService->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        try {
            cache()->forget('subscriptions-list');
        } catch (Throwable $e) {
            $this->error($e);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SubscriptionResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function show(Subscription $subscription): JsonResponse
    {
        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SubscriptionResource::make($subscription)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Subscription $subscription
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Subscription $subscription, UpdateRequest $request): JsonResponse
    {
        $result = $this->subscriptionService->update($subscription, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        try {
            cache()->forget('subscriptions-list');
        } catch (Throwable $e) {
            $this->error($e);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            SubscriptionResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        //
    }

    /**
     * @return JsonResponse
     */
    public function dropAll(): JsonResponse
    {
        $this->subscriptionService->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function truncate(): JsonResponse
    {
        $this->subscriptionService->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @return JsonResponse
     */
    public function restoreAll(): JsonResponse
    {
        $this->subscriptionService->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function emailSubscriptions(Request $request): AnonymousResourceCollection
    {
        $emailSubscriptions = EmailSubscription::with([
            'user' => fn($q) => $q->select([
                'id',
                'uuid',
                'firstname',
                'lastname',
                'email',
            ])
        ])
            ->when($request->input('user_id'), fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->input('deleted_at'), fn($q) => $q->onlyTrashed())
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 10));

        return EmailSubscriptionResource::collection($emailSubscriptions);
    }

}
