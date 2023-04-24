<?php

namespace App\Http\Controllers\API\v1\Dashboard\Payment;

use App\Helpers\ResponseError;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StripeRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Traits\ApiResponse;
use App\Traits\OnResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Redirect;
use Srmklive\PayPal\Services\PayPal;
use Str;
use Throwable;

class PayPalController extends Controller
{
    use OnResponse, ApiResponse;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function credential(array|null $payload): PayPal
    {
        $provider = new PayPal;
        $provider->setApiCredentials([
            'mode'    => data_get($payload, 'paypal_mode', 'sandbox'),
            'sandbox' => [
                'client_id'         => data_get($payload, 'paypal_sandbox_client_id'),
                'client_secret'     => data_get($payload, 'paypal_sandbox_client_secret'),
                'app_id'            => data_get($payload, 'paypal_sandbox_app_id'),
            ],
            'live' => [
                'client_id'         => data_get($payload, 'paypal_live_client_id'),
                'client_secret'     => data_get($payload, 'paypal_live_client_secret'),
                'app_id'            => data_get($payload, 'paypal_live_app_id'),
            ],
            'payment_action' => data_get($payload, 'paypal_payment_action', 'Sale'),
            'currency'       => data_get($payload, 'paypal_currency', 'USD'),
            'notify_url'     => data_get($payload, 'paypal_notify_url'),
            'locale'         => (bool)data_get($payload, 'paypal_locale', true),
            'validate_ssl'   => (bool)data_get($payload, 'paypal_validate_ssl', true),
        ]);

        $provider->getAccessToken();

        return $provider;
    }
    /**
     * process transaction.
     *
     * @param StripeRequest $request
     * @return JsonResponse
     */
    public function orderProcessTransaction(StripeRequest $request): JsonResponse
    {
        try {
            $host           = request()->getSchemeAndHttpHost();
            $payment        = Payment::where('tag', 'stripe')->first();
            $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
            $payload        = $paymentPayload?->payload;
            $order          = Order::find($request->input('order_id'));
            $totalPrice     = ceil($order->rate_total_price) * 100;

            $order->update([
                'total_price' => ($totalPrice / $order->rate) / 100
            ]);

            $provider = $this->credential($payload);

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&status=paid&order_id=$order->id",
                    'cancel_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&status=canceled&order_id=$order->id",
                ],
                'purchase_units' => [
                    [
                        'reference_id' => rand(000000, 999999),
                        'amount' => [
                            'currency_code' => Str::upper($order->currency?->title ?? data_get($payload, 'paypal_currency')),
                            'value'         => $totalPrice
                        ]
                    ]
                ]
            ]);

            if (data_get($response, 'error')) {

                $message = data_get($response, 'message', 'Something went wrong');

                return $this->onErrorResponse([
                    'code' => ResponseError::ERROR_400,
                    'data' => is_array($message) ? $message : [$message]
                ]);
            }

            $links = collect(data_get($response, 'links'));

            $checkoutNowUrl = $links->where('rel', 'payer-action')->first()?->href ?? $links->first()?->href;

            $paymentProcess = PaymentProcess::updateOrCreate([
                'user_id' => auth('sanctum')->id(),
                'order_id' => $request->input('order_id'),
            ], [
                'id' => data_get($response, 'id'),
                'data' => [
                    'url' => $checkoutNowUrl,
                    'price' => $totalPrice,
                ]
            ]);

            return $this->successResponse('success', $paymentProcess);
        } catch (Throwable $e) {
            $this->error($e);
        }

        return $this->onErrorResponse([
            'code' => ResponseError::ERROR_400,
        ]);
    }

    /**
     * success transaction.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function orderResultTransaction(Request $request): RedirectResponse
    {
        $payment        = Payment::where('tag', 'stripe')->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $orderId = (int)$request->input('order_id');

        /** @var PaymentProcess $paymentProcess */
        $paymentProcess = PaymentProcess::with([
            'order:id,status',
            'order.transaction:id,payable_type,payable_id,status',
        ])->where([
            ['order_id', $orderId]
        ])->first();

        $provider = $this->credential($payload);
        $response = $provider->showOrderDetails($paymentProcess->id);

        $to = config('app.front_url') . "orders/$orderId";

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $paymentProcess->order?->transaction?->update([
                'status' => $request->input('status'),
                'payment_trx_id' => $paymentProcess->id
            ]);

            $paymentProcess?->delete();

            return Redirect::to($to);
        }

        if (isset($response['status'])) {

            $paymentProcess->order?->transaction?->update([
                'status' => 'progress',
                'payment_trx_id' => $paymentProcess->id
            ]);

        }

        $to = config('app.front_url') . "orders/$orderId";

        return Redirect::to($to);
    }

}
