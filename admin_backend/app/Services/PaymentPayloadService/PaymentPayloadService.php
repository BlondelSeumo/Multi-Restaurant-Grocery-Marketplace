<?php

namespace App\Services\PaymentPayloadService;

use App\Helpers\ResponseError;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Services\CoreService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class PaymentPayloadService extends CoreService
{
    protected function getModelClass(): string
    {
        return PaymentPayload::class;
    }

    public function create(array $data): array
    {
        $prepareValidate = $this->prepareValidate($data);

        if (!data_get($prepareValidate, 'status')) {
            return $prepareValidate;
        }

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        try {

            $paymentPayload = $this->model()->create($data);

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $paymentPayload,
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_501,
            'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function update(int $paymentId, array $data): array
    {
        try {
            $data['payment_id'] = $paymentId;

            $prepareValidate = $this->prepareValidate($data);

            if (!data_get($prepareValidate, 'status')) {
                return $prepareValidate;
            }

            if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
                abort(403);
            }

            $paymentPayload = PaymentPayload::where('payment_id', $paymentId)->firstOrFail();
            $paymentPayload->update($data);

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $paymentPayload,
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function delete(?array $ids = []): array
    {
        $paymentPayloads = PaymentPayload::whereIn('payment_id', is_array($ids) ? $ids : [])->get();

        foreach ($paymentPayloads as $paymentPayload) {
            $paymentPayload->delete();
        }

        return [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
        ];
    }

    public function prepareValidate($data): array
    {
        $payment = Payment::where('id', data_get($data, 'payment_id'))->first();

        if ($payment->tag === 'paypal') {

            $validator = $this->paypalValidate($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];

        } else if ($payment->tag === 'stripe') {

            $validator = $this->stripe($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        } else if ($payment->tag === 'razorpay') {

            $validator = $this->razorpay($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        } else if ($payment->tag === 'paystack') {

            $validator = $this->payStack($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        } else if ($payment->tag === 'flutterWave') {

            $validator = $this->flw($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        } else if ($payment->tag === 'paytabs') {

            $validator = $this->payTabs($data);

            if ($validator->fails()) {
                return [
                    'status'    => false,
                    'code'      => ResponseError::ERROR_422,
                    'params'    => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_400,
            'message' => __('errors.' . ResponseError::ERROR_432, locale: $this->language),
        ];
    }

    /**
     * @param array $data
     * @return \Illuminate\Validation\Validator|\Illuminate\Contracts\Validation\Validator
     */
    public function paypalValidate(array $data): \Illuminate\Validation\Validator|\Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'payload.paypal_mode'                   => 'required|in:live,sandbox',
            'payload.paypal_sandbox_client_id'      => 'required|string',
            'payload.paypal_sandbox_client_secret'  => 'required|string',
            'payload.paypal_sandbox_app_id'         => 'required|string',
            'payload.paypal_live_client_id'         => 'required|string',
            'payload.paypal_live_client_secret'     => 'required|string',
            'payload.paypal_live_app_id'            => 'required|string',
            'payload.paypal_payment_action'         => 'required|in:Authorization,Order,Sale',
            'payload.paypal_currency'               => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
            'payload.paypal_locale'                 => 'required|string',
            'payload.paypal_validate_ssl'           => 'required|in:0,1',
            'payload.paypal_notify_url'             => 'string',
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function stripe(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.stripe_pk' => 'required|string',
            'payload.stripe_sk' => 'required|string',
            'payload.currency'  => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function razorpay(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.razorpay_key'    => 'required|string',
            'payload.razorpay_secret' => 'required|string',
            'payload.currency'  => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function payStack(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.paystack_pk'   => 'required|string',
            'payload.paystack_sk'   => 'required|string',
            'payload.currency'      => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function flw(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.flw_pk'        => 'required|string',
            'payload.flw_sk'        => 'required|string',
            'payload.title'         => 'required|string',
            'payload.description'   => 'required|string',
            'payload.logo'          => 'required|string',
            'payload.currency'      => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function payTabs(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.profile_id'    => 'required|string',
            'payload.server_key'    => 'required|string',
            'payload.client_key'    => 'required|string',
            'payload.currency'      => [
                'required',
                Rule::exists('currencies', 'title')->whereNull('deleted_at')
            ],
        ]);
    }

}
