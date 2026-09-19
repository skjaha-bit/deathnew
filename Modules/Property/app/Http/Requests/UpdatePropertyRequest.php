<?php

namespace Modules\Property\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:properties,id'],
            'property_type_id' => ['sometimes', 'nullable', 'integer', 'exists:property_types,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'location' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'asset_registration_number' => ['sometimes', 'nullable', 'string', 'max:64', 'regex:'.ValidationPatterns::CODE],
            'ownership' => ['sometimes', 'nullable', 'string', 'in:own_by_me,co_owned'],
            'co_owner_name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'co_owner_contact' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'deeds_location' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'has_will' => ['sometimes', 'nullable', 'boolean'],

            'lender' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'loan_number' => ['sometimes', 'nullable', 'string', 'max:64', 'regex:'.ValidationPatterns::CODE],
            'loan_outstanding_balance' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'loan_type_id' => ['sometimes', 'nullable', 'integer', 'exists:loan_types,id'],
            'insurance_company_id' => ['sometimes', 'nullable', 'integer', 'exists:insurance_companies,id'],
            'policy_number' => ['sometimes', 'nullable', 'string', 'max:64', 'regex:'.ValidationPatterns::CODE],
            'has_loan_protection' => ['sometimes', 'nullable', 'boolean'],

            'notes_text' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'This field may not contain < or > characters.',
            'location.regex' => 'This field may not contain < or > characters.',
            'asset_registration_number.regex' => 'The registration number may only contain letters, digits, spaces and hyphens.',
            'co_owner_name.regex' => 'The co-owner name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'co_owner_contact.regex' => 'This field may not contain < or > characters.',
            'deeds_location.regex' => 'This field may not contain < or > characters.',
            'lender.regex' => 'The lender may only contain letters, digits, spaces, periods, commas, ampersands, apostrophes and hyphens.',
            'loan_number.regex' => 'The loan number may only contain letters, digits, spaces and hyphens.',
            'policy_number.regex' => 'The policy number may only contain letters, digits, spaces and hyphens.',
            'notes_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
