<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;

class AddTableDataRequest extends FormRequest
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
            'data-*' => ['bail', 'nullable', 'string', 'max:65000'],
            'image-*' => ['bail', 'nullable', 'file'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'image-*' => 'Gambar',
            'data-*' => 'Data',
        ];
    }

    /**
     * Error message
     * @return array
     */
    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'string' => ':attribute harus berupa teks',
            'file' => ':attribute harus berupa file',
        ];
    }
}
