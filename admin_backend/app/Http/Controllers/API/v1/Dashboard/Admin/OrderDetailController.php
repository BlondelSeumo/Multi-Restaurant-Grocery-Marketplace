<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Resources\OrderDetailResource;
use App\Repositories\OrderRepository\OrderDetailRepository;
use App\Traits\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderDetailController extends AdminBaseController
{
    use Notification;

    private OrderDetailRepository $repository;

    /**
     * @param OrderDetailRepository $repository
     */
    public function __construct(OrderDetailRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $orderDetails = $this->repository->paginate($request->all());

        return OrderDetailResource::collection($orderDetails);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $orderDetail = $this->repository->orderDetailById($id);

        if (empty($orderDetail)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            OrderDetailResource::make($orderDetail)
        );
    }

}
