<?php

namespace App\Http\Requests\OrderRefund;

use App\Http\Requests\BaseRequest;
use App\Models\OrderRefund;
use Illuminate\Validation\Rule;

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
            'status'    => Rule::in(OrderRefund::STATUSES),
            'answer'    => [
                'string',
                request('status') === OrderRefund::STATUS_CANCELED ? 'required' : ''
            ],
        ];
    }

}
