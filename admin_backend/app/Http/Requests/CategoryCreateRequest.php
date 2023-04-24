<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'keywords'              => ['string'],
            // Проверяем если в таблице продуктов есть продукт с таким parent_id то запрещаем добавление
            'parent_id'             => ['numeric', 'exists:categories,id', 'unique:products,category_id'],
            'type'                  => ['required', Rule::in(array_keys(Category::TYPES))],
            'active'                => ['numeric', Rule::in(1,0)],
            'title'                 => ['required', 'array'],
            'title.*'               => ['required', 'string', 'min:2', 'max:191'],
            'images'                => ['array'],
            'images.*'              => ['string'],
            'description'           => ['array'],
            'description.*'         => ['string', 'min:2'],
            'meta'                  => ['array'],
            'meta.*'                => ['array'],
            'meta.*.path'           => ['string'],
            'meta.*.title'          => ['required', 'string'],
            'meta.*.keywords'       => ['string'],
            'meta.*.description'    => ['string'],
            'meta.*.h1'             => ['string'],
            'meta.*.seo_text'       => ['string'],
            'meta.*.canonical'      => ['string'],
            'meta.*.robots'         => ['string'],
            'meta.*.change_freq'    => ['string'],
            'meta.*.priority'       => ['string'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'parent_id.unique' => trans(
                'validation.item_has_product',
                ['item' => 'parent category', 'value' => 'product'],
                request('lang')
            ),
        ];
    }

}
