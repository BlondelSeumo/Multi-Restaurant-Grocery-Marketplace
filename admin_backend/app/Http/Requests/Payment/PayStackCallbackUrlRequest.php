<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;

class PayStackCallbackUrlRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'reference' => 'required|exists:transactions,payment_trx_id',
        ];
    }

}
