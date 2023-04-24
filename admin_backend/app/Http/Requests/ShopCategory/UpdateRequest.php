<?php

namespace App\Http\Requests\ShopCategory;

use App\Http\Requests\BaseRequest;
use App\Models\Category;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'categories'    => 'array|required',
            'categories.*'  => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('type', Category::MAIN)
                    ->whereNull('deleted_at')
            ]
        ];
    }
}
