<?php

namespace App\Http\Requests\Payment;

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
            'sandbox' => 'required|in:0,1',
        ];
    }

}
