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
            'users' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'name' => ['bail', 'required', 'max:255', 'unique:units'],
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
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'exists' => ':attribute tidak dapat ditemukan',
            'array' => ':attribute harus berupa array',
            'unique' => ':attribute sudah digunakan',
            'required' => ':attribute wajib diisi'
        ];
    }
}
