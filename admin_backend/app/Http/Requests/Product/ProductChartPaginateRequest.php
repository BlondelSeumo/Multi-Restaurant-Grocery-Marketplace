<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class ProductChartPaginateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status'    => Rule::in(Order::STATUSES),
            'column'    => 'regex:/^[a-zA-Z-_]+$/',
            'perPage'   => 'integer|min:1|max:100',
            'sort'      => 'string|in:asc,desc',
            'export'    => 'string|in:excel',
            'shop_id'       => [
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'search'    => 'string',
        ];
    }
}
