<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class AddRequest extends FormRequest
{
    use RequestErrorMessage;

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'access' => ['bail', 'nullable', 'in:super-admin-editor,super-admin-viewer,admin-viewer'],
            'email' => ['bail', 'required', 'string', 'max:255', 'email', 'unique:users'],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'unit' => ['bail', 'nullable', 'exists:units,id'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'email' => 'Alamat email pengguna',
            'access' => 'Akses pengguna',
            'name' => 'Nama pengguna',
            'unit' => 'Unit'
        ];
    }
}
