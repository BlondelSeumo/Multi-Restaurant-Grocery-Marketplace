<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use App\Models\Product;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $isAddon = [
            'required',
            Rule::exists('categories', 'id')->whereNull('deleted_at')
                ->where('active', true),
        ];

        return [
            'category_id'           => (int)request('addon') === 1 ? 'nullable' : $isAddon,
            'brand_id'              => ['nullable', Rule::exists('brands', 'id')
                                                        ->where('active', true)
            ],
            'unit_id'               => ['nullable', Rule::exists('units', 'id')
                                                        ->where('active', true)
            ],
            'shop_id'               => ['required', Rule::exists('shops', 'id')
                                                        ->whereNull('deleted_at')
            ],
            'status'                => Rule::in(Product::STATUSES),
            'keywords'              => 'string',
            'images'                => 'array',
            'images.*'              => 'string',
            'title'                 => ['required', 'array'],
            'title.*'               => ['required', 'string', 'min:1', 'max:191'],
            'description'           => 'array',
            'description.*'         => 'string|min:1',
            'tax'                   => 'numeric',
            'qr_code'               => ['string', Rule::unique('products','qr_code')
                ->ignore(request()->route('product'),'uuid')
            ],
            'bar_code'              => 'string',
            'active'                => 'boolean',
            'addon'                 => 'boolean',
            'min_qty'               => 'numeric',
            'max_qty'               => 'numeric',
            'meta'                  => 'array',
            'meta.*'                => 'array',
            'meta.*.path'           => 'string',
            'meta.*.title'          => 'required|string',
            'meta.*.keywords'       => 'string',
            'meta.*.description'    => 'string',
            'meta.*.h1'             => 'string',
            'meta.*.seo_text'       => 'string',
            'meta.*.canonical'      => 'string',
            'meta.*.robots'         => 'string',
            'meta.*.change_freq'    => 'string',
            'meta.*.priority'       => 'string',
        ];
    }
}
