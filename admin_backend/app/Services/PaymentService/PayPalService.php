<?php

namespace App\Services\PaymentService;

use App\Helpers\ResponseError;
use App\Models\Payout;
use App\Models\Transaction;
use App\Services\CoreService;
use Srmklive\PayPal\Services\PayPal;
use Str;
use Throwable;

class PayPalService extends CoreService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    const CREATED               = 'CREATED';
    const SAVED                 = 'SAVED';
    const APPROVED              = 'APPROVED';
    const PAYER_ACTION_REQUIRED = 'PAYER_ACTION_REQUIRED';
    const VOIDED                = 'VOIDED';
    const COMPLETED             = 'COMPLETED';

    const STATUSES = [
        self::CREATED               => self::CREATED,
        self::SAVED                 => self::SAVED,
        self::APPROVED              => self::APPROVED,
        self::PAYER_ACTION_REQUIRED => self::PAYER_ACTION_REQUIRED,
        self::VOIDED                => self::VOIDED,
        self::COMPLETED             => self::COMPLETED
    ];

    const PROGRESS_STATUSES = [
        self::CREATED               => self::CREATED,
        self::SAVED                 => self::SAVED,
        self::APPROVED              => self::APPROVED,
        self::PAYER_ACTION_REQUIRED => self::PAYER_ACTION_REQUIRED,
    ];

    /**
     * @return PayPal
     * @throws Throwable
     */
    public function provider(): PayPal
    {
        $provider = new PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        return $provider;
    }

    /**
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array
    {
        try {
            $provider = $this->provider();
//            $hook = $provider->createWebHook('https://demo-api.foodyman.org/api/v1/transactions/created', ['*']);

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => config('app.return_url') . '/' . data_get($data, 'id'),
                    'cancel_url' => config('app.cancel_url') . '/' . data_get($data, 'id'),
                ],
                'purchase_units' => [
                    [
                        'reference_id' => rand(000000, 999999),
                        'amount' => [
                            'currency_code' => Str::upper(data_get($data, 'currency', 'usd')),
                            'value' => data_get($data, 'price', '1.00')
                        ]
                    ]
                ]
            ]);

            if (data_get($response, 'error')) {
                return ['status' => false, 'message' => data_get($response, 'message', 'Something went wrong')];
            }

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $response];
        } catch (Throwable $e) {
            $this->error($e);
            return ['status' => false, 'code' => ResponseError::ERROR_501, 'message' => $e->getMessage()];
        }
    }

    public function checkOrderStatus(string|int|null $trxId): array
    {
        try {
            $provider = $this->provider();
            $response = $provider->showOrderDetails($trxId);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $response];
        } catch (Throwable $e) {
            $this->error($e);
            return ['status' => false, 'code' => ResponseError::ERROR_501, 'message' => $e->getMessage()];
        }
    }

    public function updateOrderStatus(array $paypal, Transaction $transaction) {

        $status = data_get($paypal, 'status');

        if (in_array($status,PayPalService::PROGRESS_STATUSES)) {

            $status = Transaction::STATUS_PROGRESS;

        } else if ($status === PayPalService::VOIDED) {

            $status = Transaction::STATUS_CANCELED;

        } else if ($status === PayPalService::COMPLETED) {

            $status = Transaction::STATUS_PROGRESS;

        } else {

            $status = $transaction->status;

        }

        $transaction->update([
            'payment_trx_id' => data_get($paypal, 'id'),
            'status'         => $status,
            'status_note'    => "$status/" . data_get($paypal, 'status')
        ]);
    }

}
