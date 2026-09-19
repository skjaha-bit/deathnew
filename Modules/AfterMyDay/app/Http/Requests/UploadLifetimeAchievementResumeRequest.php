<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadLifetimeAchievementResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            'resume.mimes' => 'The resume must be a pdf, doc or docx file.',
        ];
    }
}
