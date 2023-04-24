<?php

namespace App\Http\Requests\Notification;

use App\Http\Requests\BaseRequest;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type'      => ['required', 'string', Rule::in(Notification::TYPES)],
            'payload'   => 'array',
            'payload.*' => [
                request('type') === Notification::ORDER_STATUSES ? Rule::in(Order::STATUSES) : 'string'
            ],
        ];
    }
}
