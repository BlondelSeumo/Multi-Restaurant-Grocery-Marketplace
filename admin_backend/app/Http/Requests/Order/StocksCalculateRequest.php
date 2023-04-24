<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class StocksCalculateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $isRequired = 'nullable';

        if (!auth('sanctum')->user()?->hasRole(['seller'])) {
            $isRequired = 'required';
        }

        return [
            'currency_id'           => 'numeric|exists:currencies,id',
            'coupon'                => 'exists:coupons,name',
            'shop_id'               => [
                $isRequired,
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'type'                  => ['required', Rule::in(Order::DELIVERY_TYPES)],
            'address'               => request('type') === Order::DELIVERY ? 'array|required' : 'array',
            'address.latitude'      => request('type') === Order::DELIVERY ? 'numeric|required' : 'numeric',
            'address.longitude'     => request('type') === Order::DELIVERY ? 'numeric|required' : 'numeric',
            'products'              => 'required|array',
            'products.*.stock_id'   =>  [
                'required',
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')
            ],
            'products.*.quantity'   => 'required|integer',
            'products.*.parent_id'  => [
                'nullable',
                'integer',
                Rule::exists('stocks', 'id')->where('addon', 0)->whereNull('deleted_at')
            ],
        ];
    }
}
