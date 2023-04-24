<?php

namespace App\Http\Requests\ShopTag;

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
            'title'                 => 'required|array',
            'title.*'               => 'string|min:1|max:191',
            'images'                => 'array',
            'images.*'              => 'string|min:1|max:255',
        ];
    }
}
