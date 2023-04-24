<?php

namespace App\Http\Requests\DeliveryManSetting;

use App\Http\Requests\BaseRequest;

class DeliveryManUpdateLocationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'location'              => 'required|array',
            'location.latitude'     => 'required|numeric',
            'location.longitude'    => 'required|numeric',
        ];
    }
}
