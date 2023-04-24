<?php

namespace App\Http\Requests\Referral;

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
            'price_from'    => 'integer|max:21000000',
            'price_to'      => 'integer|max:21000000',
            'expired_at'    => 'date_format:Y-m-d',
            'title'         => 'array',
            'title.*'       => 'string|min:1|max:191',
            'description'   => 'array',
            'description.*' => 'string|min:1',
            'faq'           => 'array',
            'faq.*'         => 'string|min:1',
            'img'           => 'string',
        ];
    }
}
