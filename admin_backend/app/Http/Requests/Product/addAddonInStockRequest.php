<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class addAddonInStockRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'addons'   => 'array',
            'addons.*' => Rule::exists('products', 'id')->where('addon', 1),
        ];
    }
}
