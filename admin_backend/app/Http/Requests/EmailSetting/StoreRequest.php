<?php

namespace App\Http\Requests\EmailSetting;

use App\Http\Requests\BaseRequest;
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
            'smtp_auth'     => 'boolean',
            'smtp_debug'    => 'boolean',
            'host'          => 'required|string',
            'port'          => 'required|integer',
            'password'      => 'required|string',
            'from_to'       => 'required|string',
            'active'        => Rule::in(0,1),
            'from_site'     => 'string',
            'ssl'           => 'array',
        ];
    }
}
