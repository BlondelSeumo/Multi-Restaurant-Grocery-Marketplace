<?php

namespace App\Services\Interfaces;

interface OrderServiceInterface
{
    public function create(array $data);

    public function update(int $id, array $data);

    public function updateDeliveryMan(?int $orderId, int $deliveryman, ?int $shopId);

    public function destroy(?array $ids = []);
}
