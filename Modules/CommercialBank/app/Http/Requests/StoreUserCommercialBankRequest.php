<?php

namespace Modules\CommercialBank\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\CommercialBank\Models\Bank;
use Modules\Core\Support\ValidationPatterns;

class StoreUserCommercialBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** The app may send swift as a number; store it as text, trimmed. */
    protected function prepareForValidation(): void
    {
        if (is_int($this->swift) || is_string($this->swift)) {
            $this->merge(['swift' => trim((string) $this->swift)]);
        }
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['nullable', 'exists:banks,id'],
            'swift' => ['present', 'nullable', 'required_if:bank_id,'.Bank::SWIFT_REQUIRED_ID, 'string', 'max:34', 'regex:/^[A-Za-z0-9]+$/'],
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
            'swift.present' => 'The swift field must always be sent (it may be empty unless bank 6 is selected).',
            'swift.required_if' => 'Enter the SWIFT code for this bank.',
            'swift.regex' => 'SWIFT code may only contain letters and digits.',
            'accounts.*.swift_iban.regex' => 'SWIFT/IBAN must be 5-34 letters or digits, no spaces or symbols.',
            'notes_text.regex' => 'This field may not contain < or > characters.',
        ];
    }

    /** Always answer with JSON, never a redirect, whatever Accept header the client sends. */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}
