<?php

namespace App\Observers;

use App\Models\Shop;
use App\Services\DeletingService\DeletingService;
use App\Traits\Loggable;
use Cache;
use Exception;
use Illuminate\Support\Str;

class ShopObserver
{
    use Loggable;

    /**
     * Handle the Shop "creating" event.
     *
     * @param Shop $shop
     * @return void
     * @throws Exception
     */
    public function creating(Shop $shop): void
    {
        $shop->uuid = Str::uuid();

    }

    /**
     * Handle the Shop "created" event.
     *
     * @return void
     */
    public function created(): void
    {
        Cache::flush();
    }

    /**
     * Handle the Shop "updated" event.
     *
     * @param Shop $shop
     * @return void
     */
    public function updated(Shop $shop): void
    {

        if ($shop->status == 'approved') {

            if (!$shop->seller->hasRole('admin')) {
                $shop->seller?->syncRoles('seller');
            }

            $shop->seller?->invitations()?->delete();
        }

        Cache::flush();
    }

    /**
     * Handle the Shop "deleted" event.
     *
     * @param Shop $shop
     * @return void
     */
    public function deleted(Shop $shop): void
    {
        (new DeletingService)->shop($shop);

        Cache::flush();
    }

    /**
     * Handle the Shop "restored" event.
     *
     * @param Shop $shop
     * @return void
     */
    public function restored(Shop $shop): void
    {
        //
    }

}
