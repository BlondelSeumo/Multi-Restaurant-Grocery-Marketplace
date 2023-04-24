<?php

namespace App\Http\Requests;

class CategoryFilterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type'          => 'required',
            'sort'          => 'string|in:asc,desc',
            'column'        => 'string',
            'status'        => 'string',
            'perPage'       => 'numeric|min:1|max:100',
            'shop_id'       => 'numeric',
            'user_id'       => 'numeric',
            'category_id'   => 'numeric',
            'brand_id'      => 'numeric',
            'price'         => 'numeric',
            'note'          => 'string|max:255',
            'date_from'     => 'date_format:Y-m-d',
            'date_to'       => 'date_format:Y-m-d',
        ];
    }
}
