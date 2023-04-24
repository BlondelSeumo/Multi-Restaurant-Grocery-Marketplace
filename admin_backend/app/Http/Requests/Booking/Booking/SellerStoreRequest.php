<?php

namespace App\Http\Requests\Booking\Booking;

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
            'max_time'      => 'integer|max:24',
            'start_time'    => 'date_format:H:i',
            'end_time'      => 'date_format:H:i',
            'active'        => 'in:0,1',
        ];
    }
}
