<?php

namespace App\Http\Requests\Review;

use App\Http\Requests\BaseRequest;
use App\Models\Review;
use Illuminate\Validation\Rule;

class PaginateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type'          => Rule::in(Review::REVIEW_TYPES),
            'assign'        => Rule::in(array_merge(Review::ASSIGN_TYPES, ['deliveryman'])),
            'type_id'       => 'integer',
            'assign_id'     => 'integer',
            'sort'          => ['string', Rule::in(['asc', 'desc'])],
            'column'        => ['regex:/^[a-zA-Z-_]+$/'],
            'perPage'       => ['integer', 'min:1', 'max:100'],
            'user_id'       => ['integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'date_from'     => ['date_format:Y-m-d',],
            'date_to'       => ['date_format:Y-m-d',],
        ];
    }
}
