<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class addInStockRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'extras'            => 'required|array',
            'extras.*.ids'      => 'nullable|array',
            'extras.*.ids.*'    => 'integer|exists:extra_values,id',
            'extras.*.price'    => 'required|numeric',
            'extras.*.quantity' => 'required|integer',
            'extras.*.addons'   => 'array',
            'extras.*.addons.*' => Rule::exists('products', 'id')->where('addon', 1),
        ];
    }
}
