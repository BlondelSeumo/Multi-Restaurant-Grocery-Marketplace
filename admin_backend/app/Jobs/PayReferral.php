<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Settings;
use App\Models\User;
use App\Models\WalletHistory;
use App\Services\WalletHistoryService\WalletHistoryService;
use App\Traits\Loggable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class PayReferral implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Loggable;

    public ?User $user;
    public ?string $type;

    /**
     * Create a new event instance.
     *
     * @param User|null $user
     * @param string $type
     */
    public function __construct(?User $user, string $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Handle the event
     * @return void
     */
    public function handle(): void
    {
        $active = Settings::adminSettings()->where('key', 'referral_active')->first();

        if (!data_get($active, 'value')) {
            return;
        }

        $referral = Referral::where('expired_at', '>=', now())->first();
        $priceFrom = $referral->price_from;
        $priceTo   = $referral->price_to;

        if (!empty($this->user?->referral) &&
            $this->user->orders?->where('status', Order::STATUS_DELIVERED)?->count() === 1
        ) {
            $owner = User::where('my_referral', $this->user->referral)->first();

            if ($owner->my_referral !== $owner->referral || $this->user->my_referral !== $this->user->referral) {

                if ($this->type === 'increment') {
                    $this->increment($owner, $priceFrom, $priceTo);
                } else if ($this->type === 'decrement') {
                    $this->decrement($owner, $priceFrom, $priceTo);
                }

            }

        }

    }

    /**
     * @param User|null $owner
     * @param $priceFrom
     * @param $priceTo
     * @return void
     */
    private function increment(?User $owner, $priceFrom, $priceTo): void
    {

        if (!empty($owner?->wallet) && $priceFrom > 0) {

            $currency  = $owner->wallet->currency;
            $priceFrom = $priceFrom * data_get($currency, 'rate');

            $owner->wallet->increment('price', (double)$priceFrom);

            $this->transaction($owner, $priceFrom, 'referral_from_topup');
        }

        if ($this->user?->wallet && $priceTo > 0) {

            $currency  = $owner->wallet->currency;
            $priceTo   = $priceTo * data_get($currency, 'rate');

            $this->user->wallet->increment('price', (double)$priceTo);

            $this->transaction($this->user, $priceTo, 'referral_to_topup');
        }

    }

    /**
     * @param User|null $owner
     * @param $priceFrom
     * @param $priceTo
     * @return void
     */
    private function decrement(?User $owner, $priceFrom, $priceTo): void
    {

        if (!empty($owner?->wallet) && $priceFrom > 0) {

            $currency  = $owner->wallet->currency;
            $priceFrom = $priceFrom * data_get($currency, 'rate');

            $owner->wallet->decrement('price', (double)$priceFrom);

            $this->transaction($owner, $priceFrom, 'referral_from_withdraw');

        }

        if ($this->user?->wallet && $priceTo > 0) {

            $currency  = $this->user->wallet->currency;
            $priceTo   = $priceTo * data_get($currency, 'rate');

            $this->user->wallet->decrement('price', (double)$priceTo);

            $this->transaction($this->user, $priceTo, 'referral_to_withdraw');

        }

    }

    /**
     * @param User|null $user
     * @param $price
     * @param $type
     * @return void
     */
    private function transaction(?User $user, $price, $type): void
    {
        (new WalletHistoryService)->create([
            'type'      => $type,
            'price'     => (double)$price,
            'note'      => 'For referral #' . $user->id,
            'status'    => WalletHistory::PAID,
            'user'      => $user
        ]);

        $to = $user->id !== $this->user->id ? 'to' : 'from';

        $to .= str_replace('referral_to_', ' ', $type);

        $user->wallet->createTransaction([
            'price'                 => (double)$price,
            'user_id'               => $user->id,
            'payment_sys_id'        => data_get(Payment::where('tag', 'wallet')->first(), 'id'),
            'note'                  => "referral $to #{$user->wallet->id}",
            'perform_time'          => now(),
            'status_description'    => "Referral transaction for wallet #{$user->wallet->id}"
        ]);
    }
}
