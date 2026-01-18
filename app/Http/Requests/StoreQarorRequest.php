<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQarorRequest extends FormRequest
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
            'published_id' => 'required|integer|unique:qarors,published_id',
            'title' => 'required|string|max:' . config('qaror.max_title_length'),
            'pdf' => 'required|file|mimes:pdf|max:' . config('qaror.max_pdf_size'),
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
            'published_id.required' => 'ID majburiy',
            'published_id.unique' => 'Bu ID allaqachon mavjud',
            'title.required' => 'Sarlavha majburiy',
            'pdf.required' => 'PDF fayl majburiy',
            'pdf.mimes' => 'Faqat PDF format qabul qilinadi',
            'pdf.max' => 'PDF fayl hajmi ' . (config('qaror.max_pdf_size') / 1024) . 'MB dan oshmasligi kerak',
        ];
    }
}
