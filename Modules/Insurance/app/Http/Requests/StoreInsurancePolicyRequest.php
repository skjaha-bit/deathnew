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
            'documents.*' => ['file', 'max:25600', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.*.uploaded' => 'Each document must be 25 MB or smaller.',
            'documents.*.max' => 'Each document must be 25 MB or smaller.',
            'documents.*.mimes' => 'Documents must be PDF, JPG, PNG, DOC or DOCX files.',
            'other_type_text.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
