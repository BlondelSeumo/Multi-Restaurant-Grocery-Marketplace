<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Referral\StoreRequest;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\WalletHistoryResource;
use App\Models\Referral;
use App\Models\WalletHistory;
use App\Services\ReferralService\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReferralController extends AdminBaseController
{
    private ReferralService $service;

    public function __construct(ReferralService $service)
    {
        parent::__construct();
        $this->service  = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $referral = Referral::with([
            'translation' => fn($q) => $q->where('locale', $this->language),
            'translations',
            'galleries',
        ])->get();

        return ReferralResource::collection($referral);
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function transactions(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $types = [
            'referral_from_topup',
            'referral_to_topup',
            'referral_from_withdraw',
            'referral_to_withdraw'
        ];

        $referralTransactions = WalletHistory::with([
            'transaction',
            'wallet' => fn($query) =>
            $query->when($request->input('user_id'), fn($q, $userId) => $q->where('user_id', $userId)
            )
        ])
            ->when($request->input('user_id'),
                fn($query, $userId) => $query->whereHas('wallet', fn($q) => $q->where('user_id', $userId))
            )
            ->when($request->input('status'), fn($query, $status) => $query->where('status', $status))
            ->whereIn('type', $types)
            ->paginate($request->input('perPage', 10));

        return WalletHistoryResource::collection($referralTransactions);
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
            ReferralResource::make(data_get($result, 'data'))
        );

    }

    /**
     * Display a listing of the resource.
     *
     * @param Referral $referral
     * @return ReferralResource
     */
    public function show(Referral $referral): ReferralResource
    {
        return ReferralResource::make($referral->load([
            'translation' => fn($q) => $q->where('locale', $this->language),
            'translations',
            'galleries',
        ]));
    }

    /**
     * Display a listing of the resource.
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
