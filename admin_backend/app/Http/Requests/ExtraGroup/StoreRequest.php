<?php

namespace App\Http\Requests\ExtraGroup;

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
            'title'     => 'array',
            'title.*'   => 'required|string|min:2|max:191',
            'type'      => 'required|string',
            'active'    => 'boolean',
        ];
    }
}

