<?php

namespace App\Http\Requests\Ticket;

use App\Http\Requests\BaseRequest;
use App\Models\Ticket;
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
            'created_by'    => ['required', 'exists:users,id'],
            'user_id'       => ['required', 'exists:users,id'],
            'order_id'      => ['required', 'exists:orders,id'],
            'parent_id'     => ['exists:tickets,id'],
            'type'          => Rule::in(['question', 'answer']),
            'subject'       => ['required', 'string'],
            'content'       => ['required', 'string'],
            'status'        => Rule::in(Ticket::STATUS),
        ];
    }
}
