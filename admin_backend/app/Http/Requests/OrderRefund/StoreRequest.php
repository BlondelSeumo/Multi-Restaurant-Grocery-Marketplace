<?php

namespace App\Http\Requests\OrderRefund;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cause'     => 'required|string',
            'order_id'  => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where('user_id', auth('sanctum')->id())
            ],
            'images'    => ['array'],
            'images.*'  => ['string'],
        ];
    }
}
