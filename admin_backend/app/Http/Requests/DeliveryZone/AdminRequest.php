<?php

namespace App\Http\Requests\DeliveryZone;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'address'       => 'array|required',
            'address.*'     => 'array|required',
            'address.*.*'   => 'numeric|required',
            'shop_id'       => [
                'required',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
        ];
    }

}
