<?php

namespace App\Http\Requests;

class PasswordUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'min:6', 'confirmed']
        ];
    }
}
