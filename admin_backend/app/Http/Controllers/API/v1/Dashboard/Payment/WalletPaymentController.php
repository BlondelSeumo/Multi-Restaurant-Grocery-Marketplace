<?php

namespace App\Http\Controllers\API\v1\Dashboard\Payment;

use App\Helpers\ResponseError;
use App\Http\Controllers\API\v1\Dashboard\Admin\AdminBaseController;
use App\Http\Requests\Payment\PaymentTopUpRequest;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Matscode\Paystack\Transaction;
use Razorpay\Api\Api;
use Str;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Throwable;

class WalletPaymentController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function paymentTopUp(PaymentTopUpRequest $request): JsonResponse
    {
        /** @var User $user */
        $user           = auth('sanctum')->user();
        $payment        = Payment::where('tag', $request->input('payment_type'))->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();

        if (empty($user?->wallet)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_108,
                'message' => __('errors.' . ResponseError::ERROR_108)
            ]);
        }

        if (empty($payment)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_432,
                'message' => __('errors.' . ResponseError::ERROR_432)
            ]);
        }

        if (empty($paymentPayload)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_432,
                'message' => __('errors.' . ResponseError::ERROR_432)
            ]);
        }

        $payload = $paymentPayload->payload;

        $totalPrice = ceil($request->input('price')) * 100;

        /** @var Translation $transaction */
        $transaction = $user->wallet->createTransaction([
            'price'                 => $totalPrice,
            'user_id'               => $user->id,
            'payment_sys_id'        => $payment->id,
            'note'                  => "referral #{$user->wallet->id}",
            'perform_time'          => now(),
            'status_description'    => "Referral transaction for wallet #{$user->wallet->id}"
        ]);

        $host = request()->getSchemeAndHttpHost();

        try {

            if ($payment->tag === 'stripe') {

                $session = $this->stripe($payload, $totalPrice);

                return $this->successResponse(
                    'success',
                    $this->process(
                        userId: $user->id,
                        trxId:  $transaction->id,
                        id:     $session->payment_intent,
                        url:    $session->url,
                        totalPrice: $totalPrice
                    )
                );
            } else if($payment->tag === 'razorpay') {

                $paymentLink = $this->razorpay($payload, $totalPrice);

                return $this->successResponse(
                    'success',
                    $this->process(
                        userId: $user->id,
                        trxId:  $transaction->id,
                        id:     data_get($paymentLink, 'id'),
                        url:    data_get($paymentLink, 'short_url'),
                        totalPrice: $totalPrice
                    )
                );
            } else if($payment->tag === 'paystack') {

                $response = $this->payStack($payload, $totalPrice, $user);

                return $this->successResponse(
                    'success',
                    $this->process(
                        userId: $user->id,
                        trxId:  $transaction->id,
                        id:     data_get($response, 'reference'),
                        url:    data_get($response, 'authorizationUrl'),
                        totalPrice: $totalPrice
                    )
                );
            }

        } catch (Throwable $e) {
            return $this->onErrorResponse(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
    }

    /**
     * @param array $payload
     * @param int|float $totalPrice
     * @return Session|Throwable
     * @throws ApiErrorException
     */
    public function stripe(array $payload, int|float $totalPrice): Session|Throwable
    {
        Stripe::setApiKey(data_get($payload, 'stripe_sk'));

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => Str::lower(data_get($payload, 'currency')),
                        'product_data' => [
                            'name' => 'Payment'
                        ],
                        'unit_amount' => $totalPrice,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
//          'success_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}",
//          'cancel_url' => "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}",
        ]);
    }

    /**
     * @param array $payload
     * @param int|float $totalPrice
     * @return Session|Throwable
     */
    public function razorpay(array $payload, int|float $totalPrice): Session|Throwable
    {
        $key    = data_get($payload, 'razorpay_key');
        $secret = data_get($payload, 'razorpay_secret');

        $api    = new Api($key, $secret);

        return $api->paymentLink->create([
            'amount'                    => $totalPrice,
            'currency'                  => Str::upper(data_get($payload, 'currency')),
            'accept_partial'            => false,
            'first_min_partial_amount'  => $totalPrice,
            'description'               => "For topup",
//            'callback_url'              => "$host/order-razorpay-success?order_id=$order->id",
            'callback_method'           => 'get'
        ]);
    }

    /**
     * @param array $payload
     * @param int|float $totalPrice
     * @param User $user
     * @return Session|Throwable
     */
    public function payStack(array $payload, int|float $totalPrice, User $user): Session|Throwable
    {
        $payStack = new Transaction(data_get($payload, 'paystack_sk'));

        $data = [
            'email'     => $user->email,
            'amount'    => $totalPrice,
            'currency'  => Str::upper(data_get($payload, 'currency')), //ZAR
        ];

        return $payStack
//            ->setCallbackUrl("$host/order-paystack-success?trx_id=$transaction->id")
            ->initialize($data);
    }

    public function process(int $userId, int $trxId, int $id, string $url, int|float $totalPrice): Model|PaymentProcess
    {
        return PaymentProcess::updateOrCreate([
            'user_id'   => $userId,
            'trx_id'    => $trxId,
        ], [
            'id' => $id,
            'data' => [
                'url'       => $url,
                'price'     => $totalPrice,
                'type'      => 'wallet',
                'user_id'   => $userId,
                'trx_id'    => $trxId,
            ]
        ]);
    }

}
