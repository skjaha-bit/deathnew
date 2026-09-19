<?php

namespace Modules\CommercialBank\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreUserCommercialBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['nullable', 'exists:banks,id'],
            'loan_type_id' => ['nullable', 'exists:loan_types,id'],
            'bank_vault' => ['nullable', 'boolean'],
            'notes_text' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],

            'accounts' => ['nullable', 'array'],
            'accounts.*.account_type_id' => ['nullable', 'exists:account_types,id'],
            'accounts.*.account_number' => ['nullable', 'string', 'max:64', 'regex:'.ValidationPatterns::CODE],
            'accounts.*.swift_iban' => ['nullable', 'string', 'max:34', 'regex:/^[A-Za-z0-9]{5,34}$/'],
            'accounts.*.is_joint' => ['nullable', 'boolean'],

            'bonds' => ['nullable', 'array'],
            'bonds.*.bond_type_id' => ['nullable', 'exists:bond_types,id'],

            'funds' => ['nullable', 'array'],
            'funds.*.fund_type_id' => ['nullable', 'exists:fund_types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'accounts.*.swift_iban.regex' => 'SWIFT/IBAN must be 5-34 letters or digits, no spaces or symbols.',
            'notes_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
