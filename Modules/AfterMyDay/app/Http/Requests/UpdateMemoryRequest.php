<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateMemoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:memories,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'date_of_memory' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'location_notes' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],

            'new_pictures' => ['sometimes', 'array'],
            'new_pictures.*' => ['image', 'max:4096'],
            'new_documents' => ['sometimes', 'array'],
            'new_documents.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:8192'],

            'remove_picture_ids' => ['sometimes', 'array'],
            'remove_picture_ids.*' => ['integer', 'exists:memory_photos,id'],
            'remove_document_ids' => ['sometimes', 'array'],
            'remove_document_ids.*' => ['integer', 'exists:memory_documents,id'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'new_pictures.*.image' => 'Each picture must be a valid image file.',
            'new_documents.*.mimes' => 'Each document must be a pdf, doc, docx, jpg, jpeg or png file.',
        ];
    }
}
