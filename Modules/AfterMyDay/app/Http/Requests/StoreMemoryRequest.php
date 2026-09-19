<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreMemoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'date_of_memory' => ['nullable', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'location_notes' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],

            'pictures' => ['sometimes', 'array'],
            'pictures.*' => ['image', 'max:4096'],
            'documents' => ['sometimes', 'array'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'pictures.*.image' => 'Each picture must be a valid image file.',
            'documents.*.mimes' => 'Each document must be a pdf, doc, docx, jpg, jpeg or png file.',
        ];
    }
}
