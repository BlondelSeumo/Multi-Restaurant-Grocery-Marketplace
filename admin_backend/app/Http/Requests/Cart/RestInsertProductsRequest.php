<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RestInsertProductsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'               => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'user_cart_uuid'        => 'required|string|exists:user_carts,uuid',
            'products'              => 'required|array',
            'products.*.stock_id'   => [
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
