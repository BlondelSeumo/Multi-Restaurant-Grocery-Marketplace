<?php

namespace App\Http\Requests\Story;

use App\Http\Requests\BaseRequest;

class UploadFileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'files'     => 'required|array',
            'files.*'   => 'required|max:20000|min:1',
        ];
    }

}
