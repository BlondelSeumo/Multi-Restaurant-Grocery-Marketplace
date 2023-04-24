<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ImageDeleteRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tag' => [ 'required', Rule::in('background', 'logo') ],
        ];
    }

}
