<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class AddAdminRequest extends FormRequest
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
            'email' => ['bail', 'required', 'max:255', 'email:rfc,dns', 'unique:users'],
            'access' => ['bail', 'required', 'in:editor,viewer'],
            'name' => ['bail', 'required', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Alamat email pengguna',
            'access' => 'Akses pengguna',
            'name' => 'Nama pengguna',
        ];
    }

    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'unique' => ':attribute sudah digunakan',
            'required' => ':attribute wajib diisi',
            'email' => ':attribute tidak valid',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
