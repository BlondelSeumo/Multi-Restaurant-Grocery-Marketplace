<?php

namespace App\Http\Requests\DeliveryManSetting;

use App\Http\Requests\BaseRequest;
use App\Models\DeliveryManSetting;
use Illuminate\Validation\Rule;

class DeliveryManRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type_of_technique'     => ['required', 'string', Rule::in(DeliveryManSetting::TYPE_OF_TECHNIQUES)],
            'brand'                 => 'required|string',
            'model'                 => 'required|string',
            'number'                => 'required|string',
            'color'                 => 'required|string',
            'online'                => 'required|boolean',
            'location'              => 'array',
            'location.latitude'     => 'numeric',
            'location.longitude'    => 'numeric',
            'images'                => 'array',
            'images.*'              => 'string',
        ];
    }
}
