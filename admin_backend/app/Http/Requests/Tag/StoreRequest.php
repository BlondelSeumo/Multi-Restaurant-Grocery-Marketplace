<?php

namespace App\Http\Requests\Tag;

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
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->whereNull('deleted_at')
            ],
            'title'    => ['required', 'array'],
            'title.*'  => ['required', 'string', 'min:1', 'max:191'],
            'active'        => 'boolean',
        ];
    }
}
