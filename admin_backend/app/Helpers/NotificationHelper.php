<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\Settings;

class NotificationHelper
{
    public function deliveryManOrder(Order $order, ?string $language = null, string $type = 'deliveryman'): array
    {
        $km = (new Utility)->getDistance(
            optional($order->shop)->location,
            $order->location,
        );

        $second = Settings::adminSettings()->where('key', 'deliveryman_order_acceptance_time')->first();

//        $order = Order::with([
//            'user:id,uuid,firstname,lastname,email,phone,img,created_at',
//            'shop:tax,user_id,id,logo_img,location,created_at,type,uuid',
//            'shop.translation' => fn($q) => $q->select('id', 'shop_id', 'locale', 'title')->where('locale', $language),
//            'transaction:id,user_id,payable_type,price,payable_id,note,perform_time,refund_time',
//            'transaction.paymentSystem' => fn($q) => $q->select('id', 'tag', 'active'),
//        ])->find($order->id);

        return [
            'second'    => data_get($second, 'value', 30),
            'order'     => [
                'id'        => $order->id,
                'status'    => $order->status,
                'km'        => $km,
                'type'      => $type
            ],
        ];
    }
}
