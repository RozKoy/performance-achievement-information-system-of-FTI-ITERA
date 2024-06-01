<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Unique;
use App\Models\Unit;

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
            'name' => ['bail', 'required', 'max:255', new Unique(new Unit())],
            'users.old' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'users.new' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'short_name' => ['bail', 'required', 'max:10'],
        ];
    }

    public function attributes(): array
    {
        return [
            'short_name' => 'Nama pendek unit',
            'users.old' => 'Pengguna',
            'users.new' => 'Pengguna',
            'name' => 'Nama unit',
        ];
    }

    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'exists' => ':attribute tidak dapat ditemukan',
            'array' => ':attribute harus berupa array',
            'required' => ':attribute wajib diisi'
        ];
    }
}
