<?php

namespace App\Services\TransactionService;

use App\Helpers\ResponseError;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShopSubscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Services\CoreService;
use App\Services\PaymentService\PayPalService;
use Illuminate\Support\Str;

class TransactionService extends CoreService
{
    protected function getModelClass(): string
    {
        return Transaction::class;
    }

    public function orderTransaction(int $id, array $data): array
    {
        /** @var Order $order */
        $order = Order::with('user')->find($id);

        if (!$order) {
            return ['status' => true, 'code' => ResponseError::ERROR_404];
        }

        $payment = $this->checkPayment(data_get($data, 'payment_sys_id'), $order, true);

        if (!data_get($payment, 'status')) {
            return $payment;
        }

        if (data_get($payment, 'already_payed')) {
            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $order];
        }

        /** @var Transaction $transaction */
        $transaction = $order->createTransaction([
            'price'                 => $order->total_price,
            'user_id'               => $order->user_id,
            'payment_sys_id'        => data_get($data, 'payment_sys_id'),
            'payment_trx_id'        => data_get($data, 'payment_trx_id'),
            'note'                  => $order->id,
            'perform_time'          => now(),
            'status_description'    => 'Transaction for order #' . $order->id
        ]);

        if (data_get($payment, 'wallet')) {

            $this->walletHistoryAdd($order->user, $transaction, $order);

        }

        if (data_get($payment, 'payment_tag') === 'paypal') {

            $paypal = (new PayPalService)->createOrder([
                'id'        => $order->id,
                'currency'  => $order->currency?->title,
                'price'     => $order->rate_total_price
            ]);

            if (!data_get($paypal, 'status')) {
                return [
                    'status'    => data_get($paypal, 'status'),
                    'code'      => data_get($paypal, 'code'),
                    'message'   => data_get($paypal, 'message')
                ];
            }

            (new PayPalService)->updateOrderStatus(data_get($paypal, 'data'), $transaction);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => collect(data_get($paypal, 'data'))->where('rel', 'approve')->first()
            ];
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $order];
    }

    public function walletTransaction(int $id, array $data): array
    {
        $wallet = Wallet::find($id);

        if (empty($wallet)) {
            return ['status' => true, 'code' => ResponseError::ERROR_404];
        }

        $wallet->createTransaction([
            'price'                 => data_get($data, 'price'),
            'user_id'               => data_get($data, 'user_id'),
            'payment_sys_id'        => data_get($data, 'payment_sys_id'),
            'payment_trx_id'        => data_get($data, 'payment_trx_id'),
            'note'                  => $wallet->id,
            'perform_time'          => now(),
            'status_description'    => "Transaction for wallet #$wallet->id"
        ]);

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $wallet];
    }

    public function subscriptionTransaction(int $id, array $data): array
    {
        $subscription = ShopSubscription::find($id);

        if (empty($subscription)) {
            return ['status' => false, 'code' => ResponseError::ERROR_404];
        } else if ($subscription->active) {
            return ['status' => false, 'code' => ResponseError::ERROR_208];
        }

        $payment = $this->checkPayment(data_get($data, 'payment_sys_id'), request()->merge([
            'user_id'     => auth('sanctum')->id(),
            'total_price' => $subscription->price,
        ]));

        if (!data_get($payment, 'status')) {
            return $payment;
        }

        $transaction = $subscription->createTransaction([
            'price'              => $subscription->price,
            'user_id'            => auth('sanctum')->id(),
            'payment_sys_id'     => data_get($data, 'payment_sys_id'),
            'payment_trx_id'     => data_get($data, 'payment_trx_id'),
            'note'               => $subscription->id,
            'perform_time'       => now(),
            'status_description' => "Transaction for Subscription #$subscription->id"
        ]);

        if (data_get($payment, 'wallet')) {

            $subscription->update(['active' => 1]);

            $this->walletHistoryAdd(auth('sanctum')->user(), $transaction, $subscription, 'Subscription');
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $subscription];
    }

    private function checkPayment(int $id, $model, $isOrder = false): array
    {
        $payment = Payment::where('active', 1)->find($id);

        if (!$payment) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        } else if ($payment->tag !== 'wallet') {
            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'payment_tag' => $payment->tag];
        }

        if ($isOrder) {

            /** @var Order $model */
            $changedPrice = data_get($model, 'total_price', 0) - $model?->transaction?->price;

            if ($model?->transaction?->status === 'paid' && $changedPrice <= 1) {
                return ['status' => true, 'code' => ResponseError::NO_ERROR, 'already_payed' => true];
            }

            data_set($model, 'total_price', $changedPrice);
        }

        /** @var User $user */
        $user = User::with('wallet')->find(data_get($model, 'user_id'));

        if (empty($user->wallet)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_108,
                'message' => __('errors.' . ResponseError::ERROR_108, locale: $this->language)
            ];
        }

        $ratePrice = data_get($model, 'total_price', 0);

        if ($user->wallet->price >= $ratePrice) {

            $user->wallet()->update(['price' => $user->wallet->price - $ratePrice]);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'wallet' => $user->wallet];
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_109,
            'message' => __('errors.' . ResponseError::ERROR_109, locale: $this->language)
        ];

    }

    private function walletHistoryAdd(?User $user, Transaction $transaction, $model, $type = 'Order', $paymentType = 'topup')
    {
        $modelId = data_get($model, 'id');

        $user->wallet->histories()->create([
            'uuid'              => Str::uuid(),
            'transaction_id'    => $transaction->id,
            'type'              => $paymentType,
            'price'             => $transaction->price,
            'note'              => "Payment $type #$modelId via Wallet" ,
            'status'            => WalletHistory::PAID,
            'created_by'        => $transaction->user_id,
        ]);

        $transaction->update(['status' => WalletHistory::PAID]);
    }
}
