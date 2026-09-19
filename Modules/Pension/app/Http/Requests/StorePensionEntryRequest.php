<?php

namespace Modules\Pension\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StorePensionEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pension_fund_id' => ['nullable', 'integer', 'exists:pension_funds,id'],
            'custom_fund_name' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'category' => ['required', 'string', 'in:private,obligatory'],
            'from_year' => ['nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'to_year' => ['nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'still_in_payment' => ['nullable', 'boolean'],
            'estimated_credit' => ['nullable', 'string', 'max:255', 'regex:/^[0-9,.\sA-Za-z]+$/'],
            'special_rights_text' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            'from_year.regex' => 'The year must be a valid 4-digit year.',
            'to_year.regex' => 'The year must be a valid 4-digit year.',
            'custom_fund_name.regex' => 'The fund name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'estimated_credit.regex' => 'The estimated credit may only contain digits, letters, spaces, commas and periods.',
            'special_rights_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
