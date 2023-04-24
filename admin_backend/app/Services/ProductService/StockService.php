<?php

namespace App\Services\ProductService;

use App\Models\Stock;
use App\Services\CoreService;

class StockService extends CoreService
{
    protected function getModelClass(): string
    {
        return Stock::class;
    }
}
