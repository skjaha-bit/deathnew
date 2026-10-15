<?php

namespace Modules\TradeUnion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateUserTradeUnionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:user_trade_unions,id'],
            'trade_union_id' => ['sometimes', 'nullable', 'integer', 'exists:trade_unions,id'],
            'union_name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'from_month' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^(0?[1-9]|1[0-2]|\p{L}+\.?)$/u'],
            'from_year' => ['sometimes', 'nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'to_month' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^(0?[1-9]|1[0-2]|\p{L}+\.?)$/u'],
            'to_year' => ['sometimes', 'nullable', 'string', 'regex:'.ValidationPatterns::YEAR],
            'still_in_payment' => ['sometimes', 'nullable', 'boolean'],
            'department_contact' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'rights_text' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'notes_text' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
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
