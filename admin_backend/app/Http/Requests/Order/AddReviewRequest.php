<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

class AddReviewRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'rating'    => 'required|numeric',
            'comment'   => 'string',
            'images'    => 'array',
            'images.*'  => 'string',

        ];
    }
}
