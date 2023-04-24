<?php

namespace App\Http\Requests\Payout;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $createdBy = 'exists:users,id';

        if (auth('sanctum')->user()->hasRole(['admin', 'manager'])) {
            $createdBy .= '|required';
        }

        return [
            'created_by'    => $createdBy,
            'currency_id'   => 'required|exists:currencies,id',
            'payment_id'    => 'required|exists:payments,id',
            'cause'         => 'string',
            'price'         => 'required|numeric|min:0',
        ];
    }
}
