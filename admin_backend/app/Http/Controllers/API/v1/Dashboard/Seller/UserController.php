<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use App\Repositories\UserRepository\UserRepository;
use App\Services\AuthService\UserVerifyService;
use App\Services\UserServices\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends SellerBaseController
{
    private UserRepository $userRepository;
    private UserService $userService;
    private User $model;

    public function __construct(User $model, UserRepository $userRepository, UserService $userService)
    {
        parent::__construct();
        $this->userRepository   = $userRepository;
        $this->userService      = $userService;
        $this->model            = $model;
    }

    public function paginate(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $users = $this->userRepository->usersPaginate($request->merge(['role' => 'user', 'active' => true])->all());

        return UserResource::collection($users);
    }

    public function show(string $uuid): JsonResponse
    {
        $user = $this->userRepository->userByUUID($uuid);

        if (!$user) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UserResource::make($user)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['role'] = 'user';

        if (!empty(data_get($validated, 'email'))) {
            $validated['email_verified_at'] = now();
        }

        if (!empty(data_get($validated, 'phone'))) {
            $validated['phone_verified_at'] = now();
        }

        $result = $this->userService->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        (new UserVerifyService)->verifyEmail(data_get($result, 'data'));

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UserResource::make(data_get($result, 'data'))
        );
    }

    public function shopUsersPaginate(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $users = $this->model->with('roles')
            ->whereHas('invite', function ($q) {
                $q->where(['shop_id' => $this->shop->id, 'status' => Invitation::STATUS['excepted']]);
            })
            ->when($request->input('search'), function ($query, $search) {

                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->input('role'), function ($query, $role) use ($request) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when(isset($request->active), function ($q) use ($request) {
                $q->where('active', $request->input('active'));
            })
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 15));

        return UserResource::collection($users);
    }

    public function shopUserShow(string $uuid): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->userByUUID($uuid);

        if ($user && optional($user->invite)->shop_id == $this->shop->id) {
            return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), UserResource::make($user));
        }

        return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
    }

    /**
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function getDeliveryman(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $users = $this->model->with('roles')
            ->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'deliveryman');
            })
            ->whereDoesntHave('invite', function ($q) {
                $q->where('shop_id', '!=', $this->shop->id)
                    ->where('status', 3);
            })
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%');
                });
            })
            ->whereActive(1)
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 10));

        return UserResource::collection($users);
    }

    /**
     * @param $uuid
     * @return JsonResponse
     */
    public function setUserActive($uuid): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->userByUUID($uuid);

        if ($user && optional($user->invite)->shop_id == $this->shop->id) {

            $user->update(['active' => !$user->active]);

            return $this->successResponse(
                __('errors.' . ResponseError::SUCCESS, locale: $this->language),
                UserResource::make($user)
            );
        }

        return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
    }
}
