<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Notification\UserNotificationsRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Banner;
use App\Models\Like;
use App\Models\User;
use App\Repositories\UserRepository\UserRepository;
use App\Services\UserServices\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfileController extends UserBaseController
{
    private  UserRepository $userRepository;
    private  UserService $userService;

    /**
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        $result = $this->userService->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($request, 'data')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = $this->userRepository->userById(auth('sanctum')->id());

        if (empty($user)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UserResource::make($user)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $result = $this->userService->update(auth('sanctum')->user()->uuid, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UserResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function delete(): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->userByUUID(auth('sanctum')->user()->uuid);

        if (empty($user)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        if ($user->hasRole('user')) {
            $user->paymentProcess()->forceDelete();
            $user->transactions()->forceDelete();
            $user->orders()->forceDelete();
            $user->forceDelete();
        } else {
            $user->delete();
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fireBaseTokenUpdate(Request $request): JsonResponse
    {
        if (empty($request->input('firebase_token'))) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_502, 'message' => 'token is empty']);
        }

        $user = User::firstWhere('uuid', auth('sanctum')->user()->uuid);

        if (empty($user)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $tokens   = is_array($user->firebase_token) ? $user->firebase_token : [$user->firebase_token];
        $tokens[] = $request->input('firebase_token');

        $tokens = array_filter(array_values(array_unique($tokens)), fn($value) => !is_null($value) && $value !== '');

        $user->update([
            'firebase_token' => $tokens
        ]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
        );
    }

    /**
     * @param PasswordUpdateRequest $request
     * @return JsonResponse
     */
    public function passwordUpdate(PasswordUpdateRequest $request): JsonResponse
    {
        $result = $this->userService->updatePassword(
            auth('sanctum')->user()->uuid,
            $request->input('password')
        );

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            UserResource::make(data_get($result, 'data'))
        );
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function likedLooks(FilterParamsRequest $request): JsonResponse
    {
        $user = $this->userRepository->userById(auth('sanctum')->id());

        if (empty($user)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $likes = Like::where([
            'likable_type'  => Banner::class,
            'user_id'       => $user->id
        ])
            ->pluck('likable_id');

        $looks = Banner::whereIn('id', $likes)->paginate($request->input('perPage', 10));

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            BannerResource::collection($looks)
        );
    }

    public function notifications(): AnonymousResourceCollection
    {
        return NotificationResource::collection($this->userRepository->usersNotifications());
    }

    public function notificationsUpdate(UserNotificationsRequest $request): JsonResponse
    {
        $result = $this->userService->updateNotifications($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            UserResource::make(data_get($result, 'data'))
        );
    }
}
