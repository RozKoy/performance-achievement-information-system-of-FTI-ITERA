<?php

namespace App\Http\Requests\Users;

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
            'access' => ['bail', 'nullable', 'in:super-admin-editor,super-admin-viewer,admin-viewer'],
            'unit' => ['bail', 'nullable', 'exists:units,id'],
            'email' => ['bail', 'required', 'email:rfc,dns'],
            'name' => ['bail', 'required'],
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
            'exists' => ':attribute tidak dapat ditemukan',
            'required' => ':attribute wajib diisi',
            'email' => ':attribute tidak valid',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
