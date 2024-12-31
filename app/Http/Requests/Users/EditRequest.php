<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;
use App\Rules\Unique;
use App\Models\User;

class EditRequest extends FormRequest
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
            'email' => ['bail', 'required', 'string', 'max:255', 'email', new Unique(new User(), 'user')],
            'access' => ['bail', 'nullable', 'in:super-admin-editor,super-admin-viewer,admin-viewer'],
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
