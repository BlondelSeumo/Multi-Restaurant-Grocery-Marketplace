<?php

namespace App\Http\Requests\Bonus;

use App\Http\Requests\BaseRequest;
use App\Models\Bonus;
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
            'type'              => ['required', Rule::in(Bonus::TYPES)],
            'bonus_stock_id'    => [
                'required',
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')->where('addon', 0),
            ],
            'bonus_quantity'    => 'required|numeric|min:1',
            'value'             => 'required|numeric|min:1',
            'expired_at'        => 'required|date_format:Y-m-d',
            'status'            => 'boolean',
            'bonusable_id'      => [
                'required',
                'integer',
                request('type') === Bonus::TYPE_SUM ?
                    Rule::exists('shops', 'id')->whereNull('deleted_at') :
                    Rule::exists('stocks', 'id')->whereNull('deleted_at')->where('addon', 0),
            ],
        ];
    }
}
