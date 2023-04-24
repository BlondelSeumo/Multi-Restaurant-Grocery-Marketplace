<?php

namespace App\Services\PaymentService;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Shop;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Str;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Throwable;

class StripeService extends BaseService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $data
     * @return PaymentProcess|Model
     * @throws ApiErrorException|Throwable
     */
    public function orderProcessTransaction(array $data): Model|PaymentProcess
    {
        $payment = Payment::where('tag', 'stripe')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        Stripe::setApiKey(data_get($payload, 'stripe_sk'));

        $order          = Order::find(data_get($data, 'order_id'));
        $totalPrice     = ceil($order->rate_total_price) * 100;

        $order->update([
            'total_price' => ($totalPrice / $order->rate) / 100
        ]);

        $host = request()->getSchemeAndHttpHost();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => Str::lower($order->currency?->title ?? data_get($payload, 'currency')),
                        'product_data' => [
                            'name' => 'Payment'
                        ],
                        'unit_amount' => $totalPrice,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&order_id=$order->id",
            'cancel_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&order_id=$order->id",
        ]);

        return PaymentProcess::updateOrCreate([
            'user_id'   => auth('sanctum')->id(),
            'order_id'  => data_get($data, 'order_id'),
        ], [
            'id' => $session->payment_intent,
            'data' => [
                'url'   => $session->url,
                'price' => $totalPrice,
                'order_id' => $order->id
            ]
        ]);
    }

    /**
     * @param array $data
     * @param Shop $shop
     * @param $currency
     * @return Model|array|PaymentProcess
     * @throws ApiErrorException
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, $currency): Model|array|PaymentProcess
    {
        $payment = Payment::where('tag', 'stripe')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();

        Stripe::setApiKey(data_get($paymentPayload?->payload, 'stripe_sk'));

        $host           = request()->getSchemeAndHttpHost();
        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => Str::lower(data_get($paymentPayload?->payload, 'currency', $currency)),
                        'product_data' => [
                            'name' => 'Payment'
                        ],
                        'unit_amount' => ceil($subscription->price) * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => "$host/subscription-stripe-success?token={CHECKOUT_SESSION_ID}&subscription_id=$subscription->id",
            'cancel_url' => "$host/subscription-stripe-success?token={CHECKOUT_SESSION_ID}&subscription_id=$subscription->id",
        ]);

        return PaymentProcess::updateOrCreate([
            'user_id'           => auth('sanctum')->id(),
            'subscription_id'   => $subscription->id,
        ], [
            'id' => $session->payment_intent,
            'data' => [
                'url'               => $session->url,
                'price'             => ceil($subscription->price) * 100,
                'shop_id'           => $shop->id,
                'subscription_id'   => $subscription->id,
            ]
        ]);
    }
}
