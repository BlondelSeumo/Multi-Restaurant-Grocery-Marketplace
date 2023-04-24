<?php

namespace App\Http\Requests\ShopDeliverymanSetting;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AdminStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
        ] + (new StoreRequest)->rules();
    }
}
