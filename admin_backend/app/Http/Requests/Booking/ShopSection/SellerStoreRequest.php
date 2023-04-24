<?php

namespace App\Http\Requests\Booking\ShopSection;

use App\Http\Requests\BaseRequest;

class SellerStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'area'      => 'string|max:191',
            'images'    => 'array',
            'images.*'  => 'string',
            'title'     => 'required|array',
            'title.*'  => 'required|string|min:2|max:191',
        ];
    }
}
