<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FilterParamsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sort'          => 'string|in:asc,desc',
            'column'        => 'regex:/^[a-zA-Z-_]+$/',
            'status'        => 'string',
            'perPage'       => 'integer|min:1|max:100',
            'shop_id'       => [
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'user_id'       => 'exists:users,id',
            'category_id'   => 'exists:categories,id',
            'brand_id'      => 'exists:brands,id',
            'price'         => 'numeric',
            'note'          => 'string|max:255',
            'date_from'     => 'date_format:Y-m-d',
            'date_to'       => 'date_format:Y-m-d',
            'ids'           => 'array',
            'active'        => 'boolean',
        ];
    }

}
