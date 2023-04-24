<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

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
            'user_id'               => 'integer|exists:users,id',
            'currency_id'           => 'required|integer|exists:currencies,id',
            'rate'                  => 'required|numeric',
            'shop_id'               => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'delivery_fee'          => 'nullable|numeric',
            'delivery_type'         => ['required', Rule::in(Order::DELIVERY_TYPES)],
            'coupon'                => 'nullable|string',
            'location'              => 'array',
            'location.latitude'     => 'required|numeric',
            'location.longitude'    => 'required|numeric',
            'address'               => 'array',
            'phone'                 => 'string',
            'username'              => 'string',
            'delivery_date'         => 'required|date|date_format:Y-m-d',
            'delivery_time'         => 'nullable|string|date_format:H:i',
            'note'                  => 'nullable|string|max:191',
            'cart_id'               => 'integer|exists:carts,id',
            'images'                => 'array',
            'images.*'              => 'string',

            'products'              => 'nullable|array',
            'products.*.stock_id'   =>  [
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')
            ],
            'products.*.quantity'   => 'nullable|integer',
            'products.*.parent_id'  => [
                'nullable',
                'integer',
                Rule::exists('stocks', 'id')->where('addon', 0)->whereNull('deleted_at')
            ],
            'bonus'                 => 'boolean',
        ];
    }

}
