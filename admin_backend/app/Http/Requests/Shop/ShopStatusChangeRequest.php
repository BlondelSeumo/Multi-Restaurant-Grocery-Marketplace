<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;
use App\Models\Shop;
use Illuminate\Validation\Rule;

class ShopStatusChangeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
     public function rules(): array
     {
         return [
             'status' => ['required', 'string', Rule::in(Shop::STATUS)],
         ];
     }
}
