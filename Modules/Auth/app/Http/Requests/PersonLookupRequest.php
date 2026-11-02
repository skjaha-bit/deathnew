<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class PersonLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kennitala' => ['required', 'string', 'regex:'.ValidationPatterns::KENNITALA],
        ];
    }

    public function messages(): array
    {
        return [
            'kennitala.regex' => 'The ID number must be a valid kennitala, e.g. 010190-1234.',
        ];
    }
}
