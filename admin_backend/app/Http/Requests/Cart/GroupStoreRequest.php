<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class GroupStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'       => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'stock_id'          =>  [
                'required',
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')
            ],
            'quantity'          => 'required|numeric|min:1',
            'cart_id'           => 'required|integer|exists:carts,id',
            'user_cart_uuid'    => 'required|string|exists:user_carts,uuid',
            'name'              => 'string',
        ];
    }

}
