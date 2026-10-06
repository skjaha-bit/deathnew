<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class LookupRelativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kennitala' => ['required', 'string', 'regex:'.ValidationPatterns::KENNITALA],
            // When editing a member, pass its id so its own kennitala is not reported as a duplicate.
            'relative_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'kennitala.regex' => 'The ID number must be a valid kennitala, e.g. 010190-1234.',
        ];
    }
}
