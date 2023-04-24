<?php

namespace App\Http\Requests\Currency;

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
            'title'     => 'required|string',
            'symbol'    => 'required|string',
            'rate'      => 'numeric',
            'active'    => 'boolean',
        ];
    }
}

