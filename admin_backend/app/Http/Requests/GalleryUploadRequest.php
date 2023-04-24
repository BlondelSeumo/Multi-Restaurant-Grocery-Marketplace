<?php

namespace App\Http\Requests;

use App\Models\Gallery;
use Illuminate\Validation\Rule;

class GalleryUploadRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'file'],
            'type'  => ['required', 'string', Rule::in(Gallery::TYPES)],
        ];
    }
}
