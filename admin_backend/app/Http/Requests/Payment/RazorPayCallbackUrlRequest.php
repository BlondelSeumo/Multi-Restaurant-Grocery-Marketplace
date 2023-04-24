<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;

class RazorPayCallbackUrlRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'razorpay_payment_link_id'      => 'required|exists:transactions,payment_trx_id',
            'razorpay_payment_id'           => 'string',
            'razorpay_payment_link_status'  => 'string',
        ];
    }

}
