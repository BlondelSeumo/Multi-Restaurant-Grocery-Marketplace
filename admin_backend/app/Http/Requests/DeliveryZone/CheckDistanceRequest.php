<?php

namespace App\Http\Requests\DeliveryZone;

use App\Http\Requests\BaseRequest;

class CheckDistanceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'address'           => 'array|required',
            'address.latitude'  => 'numeric|required',
            'address.longitude' => 'numeric|required',
        ];
    }
}
