<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class OpenCartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cart_id'   => 'required|integer|exists:carts,id',
            'shop_id'       => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'name'      => 'required|string',
        ];
    }
}
