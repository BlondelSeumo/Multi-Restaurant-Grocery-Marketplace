<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\Cart\CalculateRequest;
use App\Http\Requests\Cart\InsertProductsRequest;
use App\Http\Requests\Cart\OpenCartOwnerRequest;
use App\Http\Requests\Cart\StoreRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\Currency;
use App\Models\User;
use App\Repositories\CartRepository\CartRepository;
use App\Services\CartService\CartService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends UserBaseController
{
    use ApiResponse;

    private CartRepository $repository;
    private CartService $service;

    /**
     * @param CartRepository $repository
     * @param CartService $service
     */
    public function __construct(CartRepository $repository, CartService $service)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->service    = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $request->input('user_in_group');
        $cart = $this->repository->get($request->input('shop_id', 0));

        if (!$cart) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CartResource::make($cart)
        );
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['rate'] = Currency::find($request->input('currency_id'))->rate;
        $result = $this->service->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * @param OpenCartOwnerRequest $request
     * @return JsonResponse
     */
    public function openCart(OpenCartOwnerRequest $request): JsonResponse
    {
        /** @var User $user */
        $data             = $request->validated();
        $user             = auth('sanctum')->user();
        $data['owner_id'] = $user->id;
        $data['user_id']  = $user->id;
        $data['name']     = $user->name_or_email;
        $data['rate']     = Currency::find($request->input('currency_id'))->rate;

        $result = $this->service->openCartOwner($data);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function delete(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->service->delete($request->input('ids', []));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function cartProductDelete(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->service->cartProductDelete($request->input('ids', []));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function userCartDelete(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->service->userCartDelete(
            $request->input('ids', []),
            $request->input('cart_id', 0)
        );

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    /**
     * @param InsertProductsRequest $request
     * @return JsonResponse
     */
    public function insertProducts(InsertProductsRequest $request): JsonResponse
    {
        $result = $this->service->insertProducts($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     */
    public function statusChange(string $uuid, Request $request): JsonResponse
    {
        $result = $this->service->statusChange($uuid, (int)$request->input('cart_id', 0));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * @param int|null $id
     * @return JsonResponse
     */
    public function setGroup(?int $id): JsonResponse
    {
        $result = $this->service->setGroup($id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            data_get($result, 'data')
        );
    }

    /**
     * @param int $id
     * @param CalculateRequest $request
     * @return JsonResponse
     */
    public function cartCalculate(int $id, CalculateRequest $request): JsonResponse
    {
        $result = $this->repository->calculateByCartId($id, $request->all());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language), data_get($result, 'data')
        );
    }

}
