<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FaqSetRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'active'        => ['bool', Rule::in([1, 2])],
            'question'      => 'array',
            'question.*'    => 'string',
            'answer'        => 'array',
            'answer.*'      => 'string',
        ];
    }
}
