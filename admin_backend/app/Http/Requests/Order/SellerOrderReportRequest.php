<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

class SellerOrderReportRequest extends BaseRequest
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
            'type'      => 'required|in:year,month,week,day',
        ];
    }
}
