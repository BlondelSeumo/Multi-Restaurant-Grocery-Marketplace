<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderChartPaginateRequest extends BaseRequest
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
            'perPage'   => 'integer|min:1|max:100',
            'export'    => 'string|in:excel',
            'date_from' => 'required|date_format:Y-m-d',
            'date_to'   => 'date_format:Y-m-d',
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
