<?php

namespace App\Observers;

use App\Models\Stock;
use App\Services\DeletingService\DeletingService;
use App\Traits\Loggable;
use Cache;

class StockObserver
{
    use Loggable;

    /**
     * Handle the Product "deleted" event.
     *
     * @param Stock $stock
     * @return void
     */
    public function deleted(Stock $stock): void
    {
        (new DeletingService)->stock($stock);
        Cache::flush();
    }
}
