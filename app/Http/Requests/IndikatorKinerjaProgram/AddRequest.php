<?php

namespace App\Http\Requests\IndikatorKinerjaProgram;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'number' => ['bail', 'required', 'numeric', 'integer', 'min:1', 'max_digits:10'],
            'definition' => ['bail', 'required', 'max:65000'],
            'columns.*' => ['bail', 'string', 'max:500'],
            'type' => ['bail', 'required', 'in:iku,ikt'],
            'name' => ['bail', 'required', 'max:65000'],
            'columns' => ['bail', 'required', 'array'],
            'file' => ['bail', 'nullable', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'definition' => 'Definisi operasional',
            'name' => 'Program strategis',
            'type' => 'Tipe pendukung',
            'columns.*' => 'Kolom',
            'columns' => 'Kolom',
            'number' => 'Nomor',
            'file' => 'File',
        ];
    }

    public function messages(): array
    {
        return [
            'max_digits' => ':attribute tidak boleh melebihi :max digit',
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'integer' => ':attribute harus berupa bilangan bulat',
            'min' => ':attribute tidak boleh kurang dari :min',
            'numeric' => ':attribute harus berupa bilangan',
            'string' => ':attribute harus berupa teks',
            'required' => ':attribute wajib diisi',
            'array' => ':attribute tidak sesuai',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
