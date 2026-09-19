<?php

namespace Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class ContactRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:insurance_policies,id'],
            'contact_preference' => ['required', 'in:call,email,no'],
            'contact_message' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_message.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
