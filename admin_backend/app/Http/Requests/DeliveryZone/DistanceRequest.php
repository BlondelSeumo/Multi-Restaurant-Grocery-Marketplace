<?php

namespace App\Http\Requests\DeliveryZone;

use App\Http\Requests\BaseRequest;

class DistanceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'origin'                => 'array|required',
            'origin.latitude'       => 'numeric|required',
            'origin.longitude'      => 'numeric|required',

            'destination'           => 'array|required',
            'destination.latitude'  => 'numeric|required',
            'destination.longitude' => 'numeric|required',
        ];
    }
}
