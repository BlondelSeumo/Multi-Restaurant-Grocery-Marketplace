<?php

namespace App\Http\Requests\Module;

use App\Http\Requests\BaseRequest;

class BookingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:zip',
        ];
    }
}
