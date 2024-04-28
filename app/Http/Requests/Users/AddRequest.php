<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'access' => ['bail', 'nullable', 'in:super-admin-editor,super-admin-viewer,admin-viewer'],
            'email' => ['bail', 'required', 'max:255', 'email:rfc,dns', 'unique:users'],
            'unit' => ['bail', 'nullable', 'exists:units,id'],
            'name' => ['bail', 'required', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Alamat email pengguna',
            'access' => 'Akses pengguna',
            'name' => 'Nama pengguna',
            'unit' => 'Unit'
        ];
    }

    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi 255 karakter',
            'exists' => ':attribute tidak dapat ditemukan',
            'unique' => ':attribute sudah digunakan',
            'required' => ':attribute wajib diisi',
            'email' => ':attribute tidak valid',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
