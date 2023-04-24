<?php

namespace App\Observers;

use App\Models\UserCart;
use Illuminate\Support\Str;

class UserCartObserver
{
    /**
     * Handle the Category "creating" event.
     *
     * @param UserCart $userCart
     * @return void
     */
    public function creating(UserCart $userCart): void
    {
        $userCart->uuid = Str::uuid();
    }
}
