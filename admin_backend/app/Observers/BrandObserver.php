<?php

namespace App\Observers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;

class BrandObserver
{
    /**
     * Handle the Category "creating" event.
     *
     * @param Brand $brand
     * @return void
     */
    public function creating(Brand $brand)
    {
        $brand->uuid = Str::uuid();
    }

    /**
     * Handle the Brand "created" event.
     *
     * @param Brand $brand
     * @return void
     */
    public function created(Brand $brand)
    {
        //
    }

    /**
     * Handle the Brand "updated" event.
     *
     * @param Brand $brand
     * @return void
     */
    public function updated(Brand $brand)
    {
        //
    }

    /**
     * Handle the Brand "deleted" event.
     *
     * @param Brand $brand
     * @return void
     */
    public function deleted(Brand $brand)
    {
        foreach (Product::where('brand_id', $brand->id)->get() as $product) {
            $product->update([
                'brand_id' => null
            ]);
        }
    }

    /**
     * Handle the Brand "restored" event.
     *
     * @param Brand $brand
     * @return void
     */
    public function restored(Brand $brand)
    {
        //
    }
}
