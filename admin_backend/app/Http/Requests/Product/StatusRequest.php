<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use App\Models\Product;
use Illuminate\Validation\Rule;

class StatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(Product::STATUSES)
            ],
        ];
    }
}
