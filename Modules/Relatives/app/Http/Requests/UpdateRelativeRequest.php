<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateRelativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'email' => ['sometimes', 'nullable', 'string', 'email:rfc,filter', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{4,20}$/'],
            'relation' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'permissions' => ['sometimes', 'nullable', 'array'],
            'permissions.*' => ['string', 'regex:'.ValidationPatterns::CODE],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'relation.regex' => 'The relation may only contain letters and spaces.',
            'phone.regex' => 'The phone number format is invalid.',
        ];
    }
}
