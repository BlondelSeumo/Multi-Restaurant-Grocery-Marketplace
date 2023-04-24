<?php

namespace App\Http\Requests\Booking\Table;

use App\Http\Requests\BaseRequest;
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
            'name'              => 'string|max:191',
            'shop_section_id'   => [
                'required',
                Rule::exists('shop_sections', 'id')
                    ->whereNull('deleted_at')
            ],
            'tax'               => 'int',
            'chair_count'       => 'string|max:191',
        ];
    }
}
