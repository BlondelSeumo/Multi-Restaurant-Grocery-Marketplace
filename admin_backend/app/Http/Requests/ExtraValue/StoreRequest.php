<?php

namespace App\Http\Requests\ExtraValue;

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
            'extra_group_id' => 'required|exists:extra_groups,id',
            'value'          => 'required|string|max:191',
            'active'         => 'boolean',
            'images'         => 'array',
            'images.0'       => 'string',
        ];
    }
}

