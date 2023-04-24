<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
	{
		return [
            'phone'     => ['numeric', 'exists:users,phone'],
            'password'  => ['required', 'string'],
            'email'     => ['email', 'exists:users,email'],
		];
	}
}
