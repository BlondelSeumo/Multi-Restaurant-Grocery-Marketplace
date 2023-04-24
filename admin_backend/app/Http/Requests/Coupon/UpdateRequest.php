<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\BaseRequest;
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
            'shop_id'       => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'name'          => 'required|string',
            'type'          => ['required', 'string', Rule::in('fix', 'percent')],
            'qty'           => 'required|numeric|min:1',
            'price'         => 'required|numeric|min:1',
            'expired_at'    => 'required|date_format:Y-m-d',
            'images'        => ['array'],
            'images.*'      => ['string'],
            'title'         => ['required', 'array'],
            'title.*'       => ['required', 'string', 'min:2', 'max:191'],
            'description'   => ['array'],
            'description.*' => ['string', 'min:2'],
        ];
    }
}

