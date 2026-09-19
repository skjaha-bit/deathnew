<?php

namespace Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreInsurancePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'insurance_company_id' => ['nullable', 'exists:insurance_companies,id'],
            'type_ids' => ['nullable', 'array'],
            'type_ids.*' => ['integer', 'exists:insurance_types,id'],
            'other_type_text' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'other_type_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
