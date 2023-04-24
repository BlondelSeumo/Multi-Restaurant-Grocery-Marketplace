<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class CalculateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type'              => ['required', Rule::in(Order::DELIVERY_TYPES)],
            'currency_id'       => Rule::exists('currencies', 'id')->whereNull('deleted_at'),
            'address'           => request('type') === Order::DELIVERY ? 'array|required' : 'array',
            'address.latitude'  => request('type') === Order::DELIVERY ? 'numeric|required' : 'numeric',
            'address.longitude' => request('type') === Order::DELIVERY ? 'numeric|required' : 'numeric',
        ];
    }
}

