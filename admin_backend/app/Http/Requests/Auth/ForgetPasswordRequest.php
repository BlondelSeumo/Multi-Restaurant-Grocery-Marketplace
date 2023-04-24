<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ForgetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
	{
		return [
            'phone' => ['required', 'numeric', 'exists:users,phone'],
		];
	}
}
