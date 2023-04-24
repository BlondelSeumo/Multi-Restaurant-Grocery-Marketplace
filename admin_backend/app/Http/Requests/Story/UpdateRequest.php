<?php

namespace App\Http\Requests\Story;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_id'    => 'required|exists:products,id',
            'active'        => 'boolean',
            'file_urls'     => 'required|array',
            'file_urls.*'   => 'required|string',
        ];
    }
}
