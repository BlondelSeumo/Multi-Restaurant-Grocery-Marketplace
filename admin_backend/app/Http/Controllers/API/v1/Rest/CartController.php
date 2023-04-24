<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\Cart\GroupStoreRequest;
use App\Http\Requests\Cart\IndexRequest;
use App\Http\Requests\Cart\OpenCartRequest;
use App\Http\Requests\Cart\RestInsertProductsRequest;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\Cart\CartResource;
use App\Repositories\CartRepository\CartRepository;
use App\Services\CartService\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends RestBaseController
{
    private CartRepository $cartRepository;
    private CartService $cartService;

    public function __construct(CartRepository $cartRepository, CartService $cartService)
    {
        parent::__construct();
        $this->cartRepository = $cartRepository;
        $this->cartService = $cartService;
    }

    public function get(int $id, IndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cart = $this->cartRepository->get(data_get($validated, 'shop_id'), $id);

        if (empty($cart)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        if (!$cart->userCarts?->where('uuid', $request->input('user_cart_uuid'))?->first()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::ERROR_400, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            CartResource::make($cart)
        );
    }

    public function openCart(OpenCartRequest $request): JsonResponse
    {
        $collection = $request->validated();

        $result = $this->cartService->openCart($collection);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    public function store(GroupStoreRequest $request): JsonResponse
    {
        $result = $this->cartService->groupCreate($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    public function insertProducts(RestInsertProductsRequest $request): JsonResponse
    {
        if (empty($request->input('user_cart_uuid'))) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_400,
                'message' => 'cart id is invalid'
            ]);
        }

        $result = $this->cartService->groupInsertProducts($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            data_get($result, 'data')
        );
    }

    public function userCartDelete(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->cartService->userCartDelete(
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

    public function cartProductDelete(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->cartService->cartProductDelete($request->input('ids', []));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function statusChange(string $userCartUuid, Request $request): JsonResponse
    {
        $result = $this->cartService->statusChange($userCartUuid, $request->input('cart_id', 0));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        $data = data_get($result, 'data');

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $data
        );
    }

}
