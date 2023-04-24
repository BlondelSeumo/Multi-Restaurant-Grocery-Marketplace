<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_sys_id' => [
                'required',
                Rule::exists('payments', 'id')->where('active', 1)
            ],
            'payment_trx_id' => 'string',
            'price'          => 'numeric',
            'user_id'        => ['integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
        ];
    }

}
