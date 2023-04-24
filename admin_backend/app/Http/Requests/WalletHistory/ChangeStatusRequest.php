<?php

namespace App\Http\Requests\WalletHistory;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => Rule::in(['rejected', 'paid']),
        ];
    }
}
