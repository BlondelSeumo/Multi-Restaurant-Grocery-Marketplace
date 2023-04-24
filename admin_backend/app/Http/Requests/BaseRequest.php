<?php

namespace App\Http\Requests;

use App\Helpers\ResponseError;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    /**
     * For method failedValidation
    */
    use ApiResponse;

    /**
     * Если хотите изменить, меняйте внутри своего класса. Этот не трогать.
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Если хотите добавить кастомные сообщения, не добавляйте сюда.
     * Сделайте parent::messages() + ['custom_column' => 'custom message']
     * @return array
     */
    public function messages(): array
    {
        return [
            'integer'       => trans('validation.integer', [], request('lang')),
            'required'      => trans('validation.required', [], request('lang')),
            'exists'        => trans('validation.exists', [], request('lang')),
            'numeric'       => trans('validation.numeric', [], request('lang')),
            'boolean'       => trans('validation.boolean', [], request('lang')),
            'bool'          => trans('validation.boolean', [], request('lang')),
            'array'         => trans('validation.array', [], request('lang')),
            'string'        => trans('validation.string', [], request('lang')),
            'expired_at'    => trans('validation.date_format', [], request('lang')),
            'date_format'   => trans('validation.date_format', [], request('lang')),
            'max'           => trans('validation.max', [], request('lang')),
            'min'           => trans('validation.min', [], request('lang')),
            'mimes'         => trans('validation.mimes', [], request('lang')),
            'in'            => trans('validation.in', [], request('lang')),
            'unique'        => trans('validation.unique', [], request('lang')),
            'email'         => trans('validation.email', [], request('lang')),
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        $response = $this->requestErrorResponse(ResponseError::ERROR_400,
            __('errors.' . ResponseError::ERROR_400, [], request('lang', 'en')),
            $errors->messages(), Response::HTTP_BAD_REQUEST);

        throw new HttpResponseException($response);
    }
}
