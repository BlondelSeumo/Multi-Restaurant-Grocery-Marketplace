<?php

namespace App\Services\OrderService;

use App\Helpers\ResponseError;
use App\Models\Order;
use App\Services\CoreService;

class OrderReviewService extends CoreService
{

    protected function getModelClass(): string
    {
        return Order::class;
    }

    public function addReview(Order $order, $collection): array
    {
        $order->addOrderReview($collection, $order->shop);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => Order::with(['reviews.assignable'])->find($order->id)
        ];
    }

    public function addDeliverymanReview($id, $collection): array
    {
        /** @var Order $order */
        $order = $this->model()
            ->with([
                'review',
                'reviews',
            ])->find($id);

        if (!$order || !$order->deliveryMan) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_400,
                'message'   => __('errors.' . ResponseError::ORDER_OR_DELIVERYMAN_IS_EMPTY, locale: $this->language)
            ];
        }

        $order->addOrderReview($collection, $order->deliveryMan);

        return [
            'status'    => true,
            'code'      => ResponseError::NO_ERROR,
            'data'      => Order::with(['reviews.assignable'])->find($order->id)
        ];
    }

    public function addReviewByDeliveryman($id, $collection): array
    {
        /** @var Order $order */
        $order = $this->model()
            ->with([
                'review',
                'reviews',
            ])->find($id);

        if (!$order || $order->deliveryMan?->id !== auth('sanctum')->id() || !$order->user) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_404,
                'message'   =>  __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ];
        }

        $order->addOrderReview($collection, $order->user);

        return [
            'status'    => true,
            'code'      => ResponseError::NO_ERROR,
            'data'      => Order::with(['reviews.assignable'])->find($order->id)
        ];
    }
}
