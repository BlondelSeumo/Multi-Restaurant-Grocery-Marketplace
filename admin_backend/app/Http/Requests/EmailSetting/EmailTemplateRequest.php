<?php

namespace App\Http\Requests\EmailSetting;

use App\Http\Requests\BaseRequest;
use App\Models\EmailTemplate;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email_setting_id'  => [
                'required',
                Rule::exists('email_settings', 'id')->where('active', 1)
            ],
            'subject'           => 'required|string',
            'body'              => 'required',
            'alt_body'          => 'required|string',
            'send_to'           => 'required|date',
            'type'              => ['required', Rule::in(EmailTemplate::TYPES)],
        ];
    }
}
