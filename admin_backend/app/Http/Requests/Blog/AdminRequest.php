<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\BaseRequest;
use App\Models\Blog;
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
        return [
            'published_at'  => 'date_format:Y-m-d',
            'active'        => 'boolean',
            'type'          => ['required', Rule::in(array_keys(Blog::TYPES))],
            'images'        => ['array'],
            'images.*'      => ['string'],
            'title'         => ['array'],
            'title.*'       => ['string', 'max:191'],
            'description'   => ['array'],
            'description.*' => ['string'],
            'short_desc'    => ['array'],
            'short_desc.*'  => ['string'],
        ];
    }
}
