<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateLifetimeAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function safeText(int $max = 2000): array
    {
        return ['sometimes', 'nullable', 'string', "max:{$max}", 'regex:'.ValidationPatterns::SAFE_TEXT];
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'place_of_birth' => $this->safeText(255),
            'personal_information' => $this->safeText(),
            'another_thing' => $this->safeText(),
            'resume' => ['sometimes', 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],

            'educations' => ['sometimes', 'nullable', 'array'],
            'educations.*.school_or_university' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'educations.*.degree' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'educations.*.year' => ['nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'educations.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'spouses' => ['sometimes', 'nullable', 'array'],
            'spouses.*.name' => ['required_with:spouses', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'spouses.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'children' => ['sometimes', 'nullable', 'array'],
            'children.*.name' => ['required_with:children', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'children.*.date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'children.*.spouse_name' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'children.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'grandchildren' => ['sometimes', 'nullable', 'array'],
            'grandchildren.*.name' => ['required_with:grandchildren', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'grandchildren.*.date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'grandchildren.*.parent_name' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'grandchildren.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'full_name.regex' => 'The full name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'spouses.*.name.regex' => 'The spouse name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'children.*.name.regex' => 'The child name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'children.*.spouse_name.regex' => "The child's spouse name may only contain letters, spaces, apostrophes, hyphens and periods.",
            'grandchildren.*.name.regex' => 'The grandchild name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'grandchildren.*.parent_name.regex' => "The grandchild's parent name may only contain letters, spaces, apostrophes, hyphens and periods.",
            'educations.*.year.regex' => 'The year must be a valid 4-digit year.',
            'resume.mimes' => 'The resume must be a pdf, doc or docx file.',
        ];
    }
}
