<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AdminUserBookingStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->whereNull('deleted_at')
            ],
        ] + (new UserBookingStoreRequest)->rules();
    }
}
