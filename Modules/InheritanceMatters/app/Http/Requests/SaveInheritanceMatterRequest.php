<?php

namespace Modules\InheritanceMatters\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class SaveInheritanceMatterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'will_exists' => ['sometimes', 'nullable', 'boolean'],
            'will_location' => ['sometimes', 'nullable', 'string', 'max:500', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'will_file' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],

            'agents' => ['sometimes', 'nullable', 'array'],
            'agents.*.name' => ['required_with:agents', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'agents.*.phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{4,20}$/'],
            'agents.*.email' => ['nullable', 'string', 'email:rfc,filter', 'max:255'],
            'agents.*.social_security_number' => ['nullable', 'string', 'regex:'.ValidationPatterns::KENNITALA],
            'agents.*.address' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'agents.*.name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'agents.*.phone.regex' => 'The phone number format is invalid.',
            'agents.*.social_security_number.regex' => 'The social security number must be a valid kennitala.',
        ];
    }
}
