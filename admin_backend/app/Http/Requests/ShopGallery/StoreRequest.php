<?php

namespace App\Http\Requests\ShopGallery;

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
            'active'    => 'required|in:0,1',
            'images'    => 'required|array',
            'images.*'  => 'string',
        ];
    }
}
