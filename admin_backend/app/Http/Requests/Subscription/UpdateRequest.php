<?php

namespace App\Http\Requests\Subscription;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
           'type'           => 'string',
           'price'          => 'required|numeric',
           'month'          => 'required|integer|min:1|max:12',
           'active'         => 'required|boolean',
           'with_report'    => 'required|boolean',
           'title'          => 'required|string',
           'product_limit'  => 'required|integer|min:1',
           'order_limit'    => 'required|integer|min:1',
        ];
    }
}
