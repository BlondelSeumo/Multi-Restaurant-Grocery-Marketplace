<?php

namespace App\Http\Requests\Cart;

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
            'cart_id'       => 'nullable|integer|exists:carts,id',
            'shop_id'       => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'stock_id'      => [
                'required',
                'integer',
                Rule::exists('stocks', 'id')->where('addon', 0)->whereNull('deleted_at')
            ],
            'quantity'      => 'required|numeric',
            'group'         => 'boolean',
            'currency_id'   => [
                'required',
                'integer',
                Rule::exists('currencies', 'id')->whereNull('deleted_at')
            ],
        ];
    }
}

