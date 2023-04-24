<?php

namespace App\Http\Requests\ShopClosedDate;

use App\Http\Requests\BaseRequest;

class SellerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'dates'     => 'array',
            'dates.*'   => 'date_format:Y-m-d',
        ];
    }
}
