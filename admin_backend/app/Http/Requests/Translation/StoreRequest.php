<?php

namespace App\Http\Requests\Translation;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

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
            'product_id'    => Rule::exists('products', 'id')->whereNull('deleted_at'),
            'group'         => 'required|string',
            'key'           => 'required|string',
            'value'         => 'array',
            'value.*'       => 'string',
        ];
    }
}
