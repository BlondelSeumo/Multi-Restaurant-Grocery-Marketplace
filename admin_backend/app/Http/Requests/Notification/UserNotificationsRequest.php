<?php

namespace App\Http\Requests\Notification;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserNotificationsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'notifications'                     => 'required|array',
            'notifications.*.notification_id'   => ['required', 'int', Rule::exists('notifications', 'id')],
            'notifications.*.active'            => 'required|boolean',
        ];
    }
}
