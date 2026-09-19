<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Core\Support\ValidationPatterns;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'email' => ['sometimes', 'string', 'email:rfc,filter', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone_code' => ['sometimes', 'nullable', 'string', 'max:8', 'regex:'.ValidationPatterns::PHONE_CODE],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:'.ValidationPatterns::PHONE_NUMBER],
            'kennitala' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:'.ValidationPatterns::KENNITALA, Rule::unique('users', 'kennitala')->ignore($this->user()->id)],
            'preferred_language_id' => ['sometimes', 'nullable', 'integer', 'exists:languages,id'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'house_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'country_id' => ['sometimes', 'nullable', 'integer', 'exists:countries,id'],
            'marital_status' => ['sometimes', 'nullable', Rule::in([
                'cohabitation', 'married', 'unmarried_partnership', 'divorced',
                'single', 'widowed', 'other', 'not_applicable', 'prefer_not_to_say',
            ])],
            'gender' => ['sometimes', 'nullable', Rule::in(['female', 'male', 'other', 'prefer_not_to_say'])],
            'newsletter_opt_in' => ['sometimes', 'boolean'],
            'emails' => ['sometimes', 'array'],
            'emails.*' => ['string', 'email:rfc,filter', 'max:255', 'distinct'],
            'phones' => ['sometimes', 'array'],
            'phones.*.phone_code' => ['required_with:phones', 'string', 'max:8', 'regex:'.ValidationPatterns::PHONE_CODE],
            'phones.*.phone_number' => ['required_with:phones', 'string', 'max:20', 'regex:'.ValidationPatterns::PHONE_NUMBER],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'phone_code.regex' => 'The phone code must be a valid dialing code, e.g. +354.',
            'phone_number.regex' => 'The phone number may only contain digits.',
            'kennitala.regex' => 'The kennitala must be 10 digits, e.g. 1234567890 or 123456-7890.',
        ];
    }
}
