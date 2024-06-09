<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Unique;
use App\Models\User;

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
            'email' => ['bail', 'required', 'string', 'max:255', 'email', new Unique(new User(), 'user')],
            'access' => ['bail', 'nullable', 'in:super-admin-editor,super-admin-viewer,admin-viewer'],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'unit' => ['bail', 'nullable', 'exists:units,id'],
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
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'exists' => ':attribute tidak dapat ditemukan',
            'string' => ':attribute harus berupa teks',
            'email.email' => ':attribute tidak valid',
            'required' => ':attribute wajib diisi',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
