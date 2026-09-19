<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeathNoticePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:4096'],
        ];
    }
}
