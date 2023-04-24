<?php

namespace App\Http\Requests\TermCondition;

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
            'title'         => 'required|array',
            'title.*'       => 'required|string|max:191',
            'description'   => 'required|array',
            'description.*' => 'required|string',
        ];
    }
}
