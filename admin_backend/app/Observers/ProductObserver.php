<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\DeletingService\DeletingService;
use App\Traits\Loggable;
use Cache;
use Illuminate\Support\Str;

class ProductObserver
{
    use Loggable;
    /**
     * Handle the Product "creating" event.
     *
     * @param Product $product
     * @return void
     */
    public function creating(Product $product): void
    {
        $product->uuid = Str::uuid();

    }

    /**
     * Handle the Product "created" event.
     *
     * @return void
     */
    public function created(): void
    {
        Cache::flush();
    }

    /**
     * Handle the Product "updated" event.
     *
     * @return void
     */
    public function updated(): void
    {
        Cache::flush();
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        (new DeletingService)->product($product);
        Cache::flush();
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param Product $product
     * @return void
     */
    public function restored(Product $product): void
    {
        //
    }

}
