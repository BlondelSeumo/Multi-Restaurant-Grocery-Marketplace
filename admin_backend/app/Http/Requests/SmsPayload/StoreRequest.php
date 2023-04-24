<?php

namespace App\Http\Requests\SmsPayload;

use App\Http\Requests\BaseRequest;
use App\Models\SmsPayload;
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
            'type' => [
                'required',
                'string',
                Rule::in(SmsPayload::TYPES),
                Rule::unique('sms_payloads', 'type')->whereNull('deleted_at')
            ],
            'default' => 'required|in:0,1',
            'payload' => 'required|array',
            'payload.*' => ['required']
        ];
    }

}
