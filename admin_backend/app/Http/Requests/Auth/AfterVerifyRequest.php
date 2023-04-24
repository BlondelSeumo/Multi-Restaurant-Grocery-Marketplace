<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AfterVerifyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
	{
		return [
            'password'  => 'string',
            'email'     => [
                'email',
                Rule::unique('users', 'email')->whereNull('email_verified_at')
            ],
            'firstname' => 'string|min:2|max:100',
            'referral'  => 'string|exists:users,my_referral|max:255',
            'gender'    => 'in:male,female',
		];
	}
}
