<?php

namespace App\Http\Requests\Units;

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
            'name' => ['bail', 'required', 'unique:units'],
            'users' => ['bail', 'nullable', 'array']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama unit',
            'users' => 'Pengguna'
        ];
    }

    public function messages(): array
    {
        return [
            'array' => ':attribute harus berupa array',
            'unique' => ':attribute sudah digunakan',
            'required' => ':attribute wajib diisi'
        ];
    }
}
