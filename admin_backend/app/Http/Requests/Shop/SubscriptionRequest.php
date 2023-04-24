<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subscription_id' => [
                'required',
                Rule::exists('subscriptions')->where('active', 1)->whereNull('deleted_at')
            ],
        ];
    }
}
