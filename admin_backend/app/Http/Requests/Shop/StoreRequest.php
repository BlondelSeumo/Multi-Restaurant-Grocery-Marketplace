<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;
use App\Models\Category;
use App\Models\Shop;
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
            'delivery_time_from'    => 'required|numeric',
            'delivery_time_to'      => 'required|numeric',
            'delivery_time_type'    => ['required', Rule::in(Shop::DELIVERY_TIME_TYPE)],
            'price'                 => 'numeric|min:0',
            'price_per_km'          => 'numeric|min:0',
            'status'                => ['string',   Rule::in(Shop::STATUS)],
            'active'                => ['numeric',  Rule::in(1,0)],
            'title'                 => 'required|array',
            'title.*'               => 'required|string|min:2|max:191',
            'description'           => 'array',
            'description.*'         => 'string|min:3',
            'address'               => 'required|array',
            'address.*'             => 'string|min:2',
            'location'              => 'array',
            'location.latitude'     => 'numeric',
            'location.longitude'    => 'numeric',
            'images'                => 'array',
            'images.*'              => 'string',
            'tags'                  => 'array',
            'tags.*'                => 'exists:shop_tags,id',
            'user_id'               => 'integer|exists:users,id',
            'tax'                   => 'numeric',
            'percentage'            => 'numeric',
            'min_amount'            => 'string',
            'phone'                 => 'string',
            'open'                  => 'in:0,1',
            'show_type'             => 'in:0,1',
            'status_note'           => 'string',
            'mark'                  => 'string',
            'type'                  => ['required', Rule::in(Shop::TYPES)],
            'categories.*'          => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('type', Category::SHOP)
                    ->whereNull('deleted_at')
            ]
        ];
    }
}
