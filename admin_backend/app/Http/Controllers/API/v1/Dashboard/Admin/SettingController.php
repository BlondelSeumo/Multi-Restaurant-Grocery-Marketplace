<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Controllers\API\v1\Rest\SettingController as RestSettingController;
use App\Models\Referral;
use App\Models\Settings;
use App\Models\User;
use App\Services\SettingService\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class SettingController extends AdminBaseController
{
    private SettingService $service;

    public function __construct(SettingService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = Settings::adminSettings();

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $settings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function store(Request $request): JsonResponse
    {
        $isRemoveRef = false;

        foreach ($request->all() as $index => $item) {
            Settings::updateOrCreate(['key' => $index],[
                'value' => $item
            ]);

            if ($index === 'referral_active' && $item) {
                $isRemoveRef = true;
            }
        }

        if ($isRemoveRef) {
            $this->clearReferral();
        }

        return $this->successResponse(
            __('errors.' . ResponseError::USER_SUCCESSFULLY_REGISTERED, locale: $this->language)
        );

    }

    /**
     * @return void
     */
    public function clearReferral(): void
    {
        $deActiveReferral = Referral::first();

        if (empty($deActiveReferral)) {
            return;
        }

        User::withTrashed()
            ->whereNotNull('my_referral')
            ->select(['my_referral', 'id'])
            ->chunkMap(function (User $user) {
                try {
                    $user->update([
                        'my_referral' => null
                    ]);
                } catch (Throwable $e) {
                    $this->error($e);
                }
            });

        try {
            cache()->delete('admin-settings');
        } catch (Throwable|InvalidArgumentException $e) {
            $this->error($e);
        }

    }

    public function systemInformation(): JsonResponse
    {
        return (new RestSettingController)->systemInformation();
    }

    public function clearCache(): JsonResponse
    {
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');

        return $this->successResponse( __('errors.' . ResponseError::SUCCESS, locale: $this->language), []);
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
