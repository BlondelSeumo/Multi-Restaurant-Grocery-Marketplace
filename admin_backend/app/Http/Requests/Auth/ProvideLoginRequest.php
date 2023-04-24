<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ProvideLoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
	{
		return [
            'email'     => 'required|email',
            'id'        => 'required|string',
            'referral'  => 'string|exists:users,my_referral|max:255',
		];
	}
}
