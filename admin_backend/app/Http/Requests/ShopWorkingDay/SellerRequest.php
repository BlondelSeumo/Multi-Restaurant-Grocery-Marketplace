<?php

namespace App\Http\Requests\ShopWorkingDay;

use App\Http\Requests\BaseRequest;
use App\Models\ShopWorkingDay;
use Illuminate\Validation\Rule;

class SellerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'dates'             => 'required|array|max:7',
            'dates.*.from'      => 'required|string|min:1|max:5',
            'dates.*.to'        => 'required|string|min:1|max:5',
            'dates.*.disabled'  => 'boolean',
            'dates.*.day'       => ['required', Rule::in(ShopWorkingDay::DAYS)],
        ];
    }
}
