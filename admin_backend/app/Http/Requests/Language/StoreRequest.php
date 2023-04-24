<?php

namespace App\Http\Requests\Language;

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
            'title'    => 'required|string',
            'locale'   => 'required|string',
            'backward' => 'boolean',
            'default'  => 'boolean',
            'active'   => 'boolean',
            'images'   => 'array',
            'images.*' => 'string',
        ];
    }
}
