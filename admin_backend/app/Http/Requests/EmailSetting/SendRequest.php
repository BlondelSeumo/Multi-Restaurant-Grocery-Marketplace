<?php

namespace App\Http\Requests\EmailSetting;

use App\Http\Requests\BaseRequest;

class SendRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subject'       => 'required|string',
            'body'          => 'required|string',
            'alt_body'      => 'required|string',
            'attachment'    => 'string',
        ];
    }
}
