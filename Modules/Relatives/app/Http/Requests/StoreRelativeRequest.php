<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Support\ValidationPatterns;

class StoreRelativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Store the kennitala as digits only, so 010190-1234 and 0101901234 count as the same person. */
    protected function prepareForValidation(): void
    {
        if (is_string($this->kennitala) && preg_match(ValidationPatterns::KENNITALA, $this->kennitala)) {
            $this->merge(['kennitala' => preg_replace('/\D/', '', $this->kennitala)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'kennitala' => [
                'nullable', 'string', 'regex:'.ValidationPatterns::KENNITALA,
                Rule::unique('relatives', 'kennitala')->where('user_id', $this->user()->id),
            ],
            'relation' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'read_access_after_death' => ['nullable', 'boolean'],
            'estate_agent_notes' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'personal_affairs_notes' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['string', 'email:rfc,filter', 'max:255', 'distinct'],
            'phones' => ['nullable', 'array'],
            'phones.*.phone_code' => ['required_with:phones', 'string', 'max:8', 'regex:'.ValidationPatterns::PHONE_CODE],
            'phones.*.phone_number' => ['required_with:phones', 'string', 'max:20', 'regex:'.ValidationPatterns::PHONE_NUMBER],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'regex:'.ValidationPatterns::CODE],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'kennitala.regex' => 'The ID number must be a valid kennitala, e.g. 010190-1234.',
            'kennitala.unique' => 'You have already added this member.',
            'relation.regex' => 'The relation may only contain letters and spaces.',
            'estate_agent_notes.regex' => 'The notes may not contain < or >.',
            'personal_affairs_notes.regex' => 'The notes may not contain < or >.',
            'phones.*.phone_code.regex' => 'The phone code must be a valid dialing code, e.g. +354.',
            'phones.*.phone_number.regex' => 'The phone number may only contain digits.',
        ];
    }
}
