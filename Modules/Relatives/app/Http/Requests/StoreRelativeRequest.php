<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreRelativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'email' => ['nullable', 'string', 'email:rfc,filter', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{4,20}$/'],
            'relation' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'permissions' => ['nullable', 'array'],
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
