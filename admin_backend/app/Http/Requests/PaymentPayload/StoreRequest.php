<?php

namespace App\Http\Requests\PaymentPayload;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Cache;
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
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return [
            'payment_id' => [
                'required',
                'integer',
                Rule::exists('payments', 'id')->whereNull('deleted_at')
                    ->whereNotIn('tag',['wallet', 'cash']),
                Rule::unique('payment_payloads', 'payment_id')->whereNull('deleted_at')
            ],
            'payload' => 'required|array',
            'payload.*' => ['required']
        ];
    }

}
