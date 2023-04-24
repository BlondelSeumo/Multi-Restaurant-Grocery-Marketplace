<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PaymentTopUpRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_type' => ['required', Rule::exists('payments', 'tag')],
            'price'        => 'required|numeric',
        ];
    }

}
