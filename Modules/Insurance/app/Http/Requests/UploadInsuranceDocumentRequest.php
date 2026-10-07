<?php

namespace Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadInsuranceDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:insurance_policies,id'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => ['file', 'max:25600', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.*.uploaded' => 'Each document must be 25 MB or smaller.',
            'documents.*.max' => 'Each document must be 25 MB or smaller.',
            'documents.*.mimes' => 'Documents must be PDF, JPG, PNG, DOC or DOCX files.',
        ];
    }
}
