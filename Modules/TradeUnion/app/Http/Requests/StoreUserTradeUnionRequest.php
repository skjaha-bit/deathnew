<?php

namespace Modules\TradeUnion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreUserTradeUnionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trade_union_id' => ['nullable', 'integer', 'exists:trade_unions,id'],
            'union_name' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'from_month' => ['nullable', 'string', 'max:20', 'regex:/^(0?[1-9]|1[0-2]|\p{L}+\.?)$/u'],
            'from_year' => ['nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'to_month' => ['nullable', 'string', 'max:20', 'regex:/^(0?[1-9]|1[0-2]|\p{L}+\.?)$/u'],
            'to_year' => ['nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'still_in_payment' => ['nullable', 'boolean'],
            'department_contact' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'rights_text' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'notes_text' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            'union_name.regex' => 'The union name may only contain letters, digits, spaces, periods, commas, ampersands, apostrophes, hyphens and parentheses.',
            'from_year.regex' => 'The year must be a valid 4-digit year.',
            'to_year.regex' => 'The year must be a valid 4-digit year.',
            'department_contact.regex' => 'This field may only contain letters and spaces.',
            'rights_text.regex' => 'This field may not contain < or > characters.',
            'notes_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
