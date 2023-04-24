<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\User;

class Notification
{
    public function order(Order $order, User $user, ?string $language = null): array
    {
        return [
            'km'            => (new Utility)->getDistance($order->shop->location, $user->deliveryManSetting->location),
            'second'        => 30,
            'order'         => $order->loadMissing([
                'shop:id,uuid,logo_img',
                'shop.translation' => fn($q) => $q->where('locale', $language),
                'user:id,uuid,firstname,lastname',
                'transaction.paymentSystem'
            ]),
        ];
    }
}
