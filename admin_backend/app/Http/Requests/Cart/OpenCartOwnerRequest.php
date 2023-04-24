<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class OpenCartOwnerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'       => ['required', 'integer', Rule::exists('shops', 'id')->whereNull('deleted_at')],
            'currency_id'   => ['required', Rule::exists('currencies', 'id')->whereNull('deleted_at')],
        ];
    }
}
