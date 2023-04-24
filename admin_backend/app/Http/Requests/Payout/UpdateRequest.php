<?php

namespace App\Http\Requests\Payout;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'created_by'    => 'exists:users,id',
            'currency_id'   => 'exists:currencies,id',
            'payment_id'    => 'exists:payments,id',
            'cause'         => 'string',
            'answer'        => 'string',
            'price'         => 'numeric|min:0',
        ];
    }
}
