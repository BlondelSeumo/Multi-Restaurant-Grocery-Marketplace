<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ProductChartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date_from' => 'required|date_format:Y-m-d',
            'date_to'   => 'date_format:Y-m-d',
            'type'      => 'required|in:year,month,day',
            'chart'     => 'in:count,price,quantity,products_count',
            'shop_id'       => [
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'column'    => 'regex:/^[a-zA-Z-_]+$/',
            'sort'      => 'string|in:asc,desc',
            'search'    => 'string',
        ];
    }
}
