<?php

namespace App\Services\PaymentService;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Shop;
use App\Models\Subscription;
use DB;
use Illuminate\Database\Eloquent\Model;
use Razorpay\Api\Api;
use Str;
use Throwable;

class RazorPayService extends BaseService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $data
     * @return PaymentProcess|Model
     * @throws Throwable
     */
    public function orderProcessTransaction(array $data): Model|PaymentProcess
    {
        return DB::transaction(function () use ($data) {

            $host           = request()->getSchemeAndHttpHost();

            $payment        = Payment::where('tag', 'razorpay')->first();
            $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
            $payload        = $paymentPayload?->payload;

            $key            = data_get($paymentPayload?->payload, 'razorpay_key');
            $secret         = data_get($paymentPayload?->payload, 'razorpay_secret');

            $api            = new Api($key, $secret);

            $order          = Order::find(data_get($data, 'order_id'));

            $totalPrice     = ceil($order->rate_total_price) * 100;

            $paymentLink    = $api->paymentLink->create([
                'amount'                    => $totalPrice,
                'currency'                  => Str::upper($order->currency?->title ?? data_get($payload, 'currency')),
                'accept_partial'            => false,
                'first_min_partial_amount'  => $totalPrice,
                'description'               => "For #$order->id",
                'callback_url'              => "$host/order-razorpay-success?order_id=$order->id",
                'callback_method'           => 'get'
            ]);

            $order->update([
                'total_price' => ($totalPrice / $order->rate) / 100
            ]);

            return PaymentProcess::updateOrCreate([
                'user_id'   => auth('sanctum')->id(),
                'order_id'  => data_get($data, 'order_id'),
            ], [
                'id'    => data_get($paymentLink, 'id'),
                'data'  => [
                    'url'   => data_get($paymentLink, 'short_url'),
                    'price' => $totalPrice,
                    'order_id' => $order->id
                ]
            ]);
        });
    }

    /**
     * @param array $data
     * @param Shop $shop
     * @param $currency
     * @return Model|PaymentProcess
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, $currency): Model|PaymentProcess
    {
        /** @var Shop $shop */
        $payment        = Payment::where('tag', 'razorpay')->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $key            = data_get($payload, 'razorpay_key');
        $secret         = data_get($payload, 'razorpay_secret');

        $api            = new Api($key, $secret);

        $host           = request()->getSchemeAndHttpHost();

        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $totalPrice     = ceil($subscription->price) * 100;

        $paymentLink    = $api->paymentLink->create([
            'amount'                    => $totalPrice,
            'currency'                  => Str::upper($currency),
            'accept_partial'            => false,
            'first_min_partial_amount'  => $totalPrice,
            'description'               => "For Subscription",
            'callback_url'              => "$host/subscription-razorpay-success?subscription_id=$subscription->id",
            'callback_method'           => 'get'
        ]);

        return PaymentProcess::updateOrCreate([
            'user_id'   => auth('sanctum')->id(),
            'order_id'  => data_get($data,'order_id'),
        ], [
            'id'    => data_get($paymentLink, 'id'),
            'data'  => [
                'url'   => data_get($paymentLink, 'short_url'),
                'price' => $totalPrice,
                'shop_id' => $shop->id
            ]
        ]);
    }
}
