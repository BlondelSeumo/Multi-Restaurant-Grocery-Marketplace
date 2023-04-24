<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserBookingStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'table_id' => [
                'required',
                Rule::exists('tables', 'id')->whereNull('deleted_at')
            ],
            'booking_id' => [
                'required',
                Rule::exists('bookings', 'id')->whereNull('deleted_at')
            ],
            'start_date' => 'date|date_format:Y-m-d H:i',
            'end_date'   => 'date|date_format:Y-m-d H:i'
        ];
    }
}
