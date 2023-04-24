<?php

namespace App\Observers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class UserObserver
{
    /**
     * Handle the Shop "creating" event.
     *
     * @param User $user
     * @return void
     * @throws InvalidArgumentException
     */
    public function creating(User $user): void
    {
        $myReferral = Str::random(2) . $user->id . Str::random(2);

        if (Str::length($myReferral) > 8) {
            $myReferral = Str::limit($myReferral, 8);
        } else if (Str::length($myReferral) < 8) {
            $myReferral .= Str::random(8 - Str::length($myReferral));
        }

        $user->uuid         = Str::uuid();
        $user->my_referral  = Str::upper($myReferral);
    }

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        $user->point()->create();
    }

    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user): void
    {
        //
    }

}
