<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
            'name' => ['bail', 'required'],
            'users.old' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'users.new' => ['bail', 'nullable', 'array', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama unit',
            'users.old' => 'Pengguna',
            'users.new' => 'Pengguna'
        ];
    }

    public function messages(): array
    {
        return [
            'exists' => ':attribute tidak dapat ditemukan',
            'array' => ':attribute harus berupa array',
            'required' => ':attribute wajib diisi'
        ];
    }
}
