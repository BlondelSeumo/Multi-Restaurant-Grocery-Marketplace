<?php

namespace App\Http\Requests\ShopDeliverymanSetting;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type'   => 'required|in:fix,percent',
            'value'  => 'required|integer',
            'period' => 'required|integer',
        ];
    }
}
