<?php

namespace App\Repositories\Interfaces;

use App\Http\Resources\OrderResource;
use App\Models\Order;

interface OrderRepoInterface
{
    public function ordersList(array $filter = []);

    public function ordersPaginate(array $filter = []);

    public function orderStocksCalculate(array $filter);

    public function orderById(int $id, int $shopId = null);

    public function reDataOrder(?Order $order): OrderResource|null;

}
