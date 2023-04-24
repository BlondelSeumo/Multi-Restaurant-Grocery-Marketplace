<?php

namespace App\Http\Requests\Discount;

use App\Http\Requests\BaseRequest;
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
        $shop = null;

        if (isset(auth('sanctum')->user()->shop)) {

            $shop = auth('sanctum')->user()->shop;

        } elseif (
            isset(auth('sanctum')->user()->moderatorShop) && (
                (auth('sanctum')->user())->role == 'moderator' ||
                (auth('sanctum')->user())->role == 'deliveryman'
            )
        ) {
            $shop = auth('sanctum')->user()->moderatorShop;
        }

        return [
            'type'          => 'required|in:fix,percent',
            'price'         => 'numeric',
            'start'         => 'date_format:Y-m-d',
            'end'           => 'required|date_format:Y-m-d',
            'active'        => 'boolean',
            'images'        => 'array',
            'images.*'      => 'required|string',
            'products'      => 'array|required',
            'products.*'    => [
                'required',
                Rule::exists('products', 'id')
                    ->whereNull('deleted_at')
                    ->where('shop_id', data_get($shop, 'id'))
                    ->whereNotNull('shop_id')
            ],
        ];
    }
}
