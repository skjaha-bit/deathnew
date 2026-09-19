<?php

namespace Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateInsurancePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:insurance_policies,id'],
            'insurance_company_id' => ['sometimes', 'nullable', 'exists:insurance_companies,id'],
            'type_ids' => ['sometimes', 'nullable', 'array'],
            'type_ids.*' => ['integer', 'exists:insurance_types,id'],
            'other_type_text' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'documents' => ['sometimes', 'nullable', 'array'],
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
