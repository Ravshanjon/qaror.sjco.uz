<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportQarorRequest extends FormRequest
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
            'file' => 'required|mimes:xlsx,csv|max:' . config('qaror.max_excel_size'),
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
            'file.required' => 'Excel fayl majburiy',
            'file.mimes' => 'Faqat XLSX yoki CSV format qabul qilinadi',
            'file.max' => 'Fayl hajmi ' . (config('qaror.max_excel_size') / 1024) . 'MB dan oshmasligi kerak',
        ];
    }
}
