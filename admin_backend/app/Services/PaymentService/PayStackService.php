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
use Illuminate\Database\Eloquent\Model;
use Matscode\Paystack\Transaction;
use Str;
use Throwable;

class PayStackService extends BaseService
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
        $payment = Payment::where('tag', 'paystack')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $transaction    = new Transaction(data_get($payload, 'paystack_sk'));

        $order          = Order::find(data_get($data, 'order_id'));
        $totalPrice     = ceil($order->rate_total_price) * 100;

        $order->update([
            'total_price' => ($totalPrice / $order->rate) / 100
        ]);

        $host = request()->getSchemeAndHttpHost();

        $data = [
            'email'     => $order->user?->email,
            'amount'    => $totalPrice,
            'currency'  => Str::upper($order->currency?->title ?? data_get($payload, 'currency')),
        ];

        $response = $transaction
            ->setCallbackUrl("$host/order-paystack-success?order_id=$order->id")
            ->initialize($data);

        if (isset($response?->status) && !data_get($response, 'status')) {
            throw new Exception(data_get($response, 'message', 'PayStack server error'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'   => auth('sanctum')->id(),
            'order_id'  => $order->id,
        ], [
            'id' => data_get($response, 'reference'),
            'data' => [
                'url'   => data_get($response, 'authorizationUrl'),
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
     * @throws Exception
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, $currency): Model|array|PaymentProcess
    {
        $payment = Payment::where('tag', 'paystack')->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host           = request()->getSchemeAndHttpHost();
        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $transaction    = new Transaction(data_get($payload, 'paystack_sk'));
        $totalPrice     = ceil($subscription->price) * 100;

        $data = [
            'email'     => $shop->seller?->email,
            'amount'    => $totalPrice,
            'currency'  => Str::upper($currency ?? data_get($payload, 'currency'))
        ];

        $response = $transaction
            ->setCallbackUrl("$host/subscription-paystack-success?subscription_id=$subscription->id")
            ->initialize($data);

        if (isset($response?->status) && !data_get($response, 'status')) {
            throw new Exception(data_get($response, 'message', 'PayStack server error'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'           => auth('sanctum')->id(),
            'subscription_id'   => $subscription->id,
        ], [
            'id' => data_get($response, 'reference'),
            'data' => [
                'url'               => data_get($response, 'authorizationUrl'),
                'price'             => $totalPrice,
                'shop_id'           => $shop->id,
                'subscription_id'   => $subscription->id,
            ]
        ]);
    }
}
