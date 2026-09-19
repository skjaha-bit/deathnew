<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreWorkplaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'contact_name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'contact_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'notify_after_death' => ['nullable', 'boolean'],
            'message_to_contact' => ['nullable', 'string', 'max:2000'],
            'additional_note' => ['nullable', 'string', 'max:2000'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['string', 'email:rfc,filter', 'max:255', 'distinct'],
            'phones' => ['nullable', 'array'],
            'phones.*.phone_code' => ['required_with:phones', 'string', 'max:8', 'regex:'.ValidationPatterns::PHONE_CODE],
            'phones.*.phone_number' => ['required_with:phones', 'string', 'max:20', 'regex:'.ValidationPatterns::PHONE_NUMBER],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.regex' => 'The workplace name contains invalid characters.',
            'contact_name.regex' => 'The contact name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'phones.*.phone_code.regex' => 'The phone code must be a valid dialing code, e.g. +354.',
            'phones.*.phone_number.regex' => 'The phone number may only contain digits.',
        ];
    }
}
