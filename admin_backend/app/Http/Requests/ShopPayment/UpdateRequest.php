<?php

namespace App\Http\Requests\ShopPayment;

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
            'payment_id'        => 'required|integer|exists:payments,id',
            'status'            => 'required|boolean',
            'client_id'         => 'nullable|string',
            'secret_id'         => 'nullable|string',
        ];
    }
}
