<?php

namespace App\Http\Requests\Receipt;

use App\Http\Requests\BaseRequest;
use App\Models\Category;
use App\Models\Receipt;
use Illuminate\Validation\Rule;

class ShopStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'images'                    => 'array',
            'images.*'                  => 'string',
            'title'                     => 'array',
            'title.*'                   => 'string|max:191',
            'description'               => 'array',
            'description.*'             => 'string',
            'ingredient'                => 'array',
            'ingredient.*'              => 'string',
            'instruction'               => 'array',
            'instruction.*'             => 'string',
            'nutrition'                 => 'array',
            'nutrition.*'               => 'array',
            'nutrition.*.weight'        => 'string|max:10',
            'nutrition.*.percentage'    => 'integer|max:100',
            'nutrition.*.*'             => 'string|max:191',
            'discount_type'             => ['required', Rule::in([Receipt::DISCOUNT_TYPE_FIX, Receipt::DISCOUNT_TYPE_PERCENT])],
            'discount_price'            => 'required|numeric|min:0',
            'stocks'                    => 'required|array',
            'category_id'               => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->whereNull('deleted_at')
                    ->where('type', Category::RECEIPT)
                    ->where('active', true)
            ],
            'active_time'               => 'required|string|max:10',
            'total_time'                => 'required|string|max:10',
            'calories'                  => 'required|integer|min:0',
            'servings'                  => 'required|integer|min:0',
            'stocks.*.stock_id'         => [
                'required',
                Rule::exists('stocks', 'id')->whereNull('deleted_at'),
                'distinct'
            ],
            'stocks.*.min_quantity'     => 'required|integer|min:1',
        ];
    }
}
