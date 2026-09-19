<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateDeathNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preference' => ['sometimes', 'nullable', 'string', 'in:yes,no,relatives_choose'],
            'obituary_media_preference' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'symbol' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::CODE],
            'introduction' => ['sometimes', 'nullable', 'string', 'max:160', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'home_address' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'body_text' => ['sometimes', 'nullable', 'string', 'max:380', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'alignment' => ['sometimes', 'nullable', 'string', 'in:left,right,center'],
            'layout' => ['sometimes', 'nullable', 'string', 'in:single,two_column'],

            'relatives' => ['sometimes', 'nullable', 'array'],
            'relatives.*.name' => ['required_with:relatives', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'relatives.*.column_position' => ['nullable', 'integer', 'min:0'],
            'relatives.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'relatives.*.name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
        ];
    }
}
