<?php

namespace App\Services\PaymentService;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Shop;
use App\Models\Subscription;
use Exception;
use Http;
use Illuminate\Database\Eloquent\Model;
use Str;
use Throwable;

class FlutterWaveService extends BaseService
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
        $payment = Payment::where('tag', 'flutterWave')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $order          = Order::find(data_get($data, 'order_id'));
        $totalPrice     = ceil($order->rate_total_price) * 100;

        $order->update([
            'total_price' => ($totalPrice / $order->rate) / 100
        ]);

        $host = request()->getSchemeAndHttpHost();

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . data_get($payload, 'flw_sk')
        ];

        $trxRef = "$order->id-" . time();

        $data = [
            'tx_ref'            => $trxRef,
            'amount'            => 100,
            'currency'          => Str::upper($order->currency?->title ?? data_get($payload, 'currency')),
            'payment_options'   => 'card,account,ussd,mobilemoneyghana',
            'redirect_url'      => "$host/order-stripe-success?order_id=$order->id",
            'customer'          => [
                'name'          => $order->username ?? "{$order->user?->firstname} {$order->user?->lastname}",
                'phonenumber'   => $order->phone ?? $order->user?->phone,
                'email'         => $order->user?->email
            ],
            'customizations'    => [
                'title'         => data_get($payload, 'title', ''),
                'description'   => data_get($payload, 'description', ''),
                'logo'          => data_get($payload, 'logo', ''),
            ]
        ];

        $request = Http::withHeaders($headers)->post('https://api.flutterwave.com/v3/payments', $data);

        $body = json_decode($request->body());

        if (data_get($body, 'status') === 'error') {
            throw new Exception(data_get($body, 'message'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'   => auth('sanctum')->id(),
            'order_id'  => data_get($data, 'order_id'),
        ], [
            'id'    => $trxRef,
            'data'  => [
                'url'       => $body,
                'price'     => $totalPrice,
                'order_id'  => $order->id
            ]
        ]);
    }

    /**
     * @param array $data
     * @param Shop $shop
     * @param $currency
     * @return Model|array|PaymentProcess
     * @throws Exception
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, $currency): Model|array|PaymentProcess
    {
        $payment = Payment::where('tag', 'stripe')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host           = request()->getSchemeAndHttpHost();

        /** @var Subscription $subscription */
        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . data_get($payload, 'flw_sk')
        ];

        $trxRef = "$subscription->id-" . time();

        $data = [
            'tx_ref'            => $trxRef,
            'amount'            => 100,
            'currency'          => Str::lower(data_get($paymentPayload?->payload, 'currency', $currency)),
            'payment_options'   => 'card,account,ussd,mobilemoneyghana',
            'redirect_url'      => "$host/subscription-stripe-success?subscription_id=$subscription->id",
            'customer'          => [
                'name'          => "{$shop->seller?->firstname} {$shop->seller?->lastname}",
                'phonenumber'   => $shop->seller?->phone,
                'email'         => $shop->seller?->email
            ],
            'customizations'    => [
                'title'         => data_get($payload, 'title', ''),
                'description'   => data_get($payload, 'description', ''),
                'logo'          => data_get($payload, 'logo', ''),
            ]
        ];

        $request = Http::withHeaders($headers)->post('https://api.flutterwave.com/v3/payments', $data);

        $body    = json_decode($request->body());

        if (data_get($body, 'status') === 'error') {
            throw new Exception(data_get($body, 'message'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'   => auth('sanctum')->id(),
            'order_id'  => data_get($data, 'order_id'),
        ], [
            'id'    => $trxRef,
            'data'  => [
                'url'               => $body,
                'price'             => ceil($subscription->price) * 100,
                'shop_id'           => $shop->id,
                'subscription_id'   => $subscription->id
            ]
        ]);
    }
}
