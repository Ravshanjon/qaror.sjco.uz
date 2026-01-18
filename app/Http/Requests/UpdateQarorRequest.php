<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQarorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pdf' => 'nullable|file|mimes:pdf|max:' . config('qaror.max_pdf_size'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pdf.mimes' => 'Faqat PDF format qabul qilinadi',
            'pdf.max' => 'PDF fayl hajmi ' . (config('qaror.max_pdf_size') / 1024) . 'MB dan oshmasligi kerak',
        ];
    }
}
