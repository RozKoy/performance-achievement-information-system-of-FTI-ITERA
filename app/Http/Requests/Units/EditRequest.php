<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;
use App\Rules\Unique;
use App\Models\Unit;

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
            'name' => ['bail', 'required', 'string', 'max:255', new Unique(new Unit(), 'unit')],
            'users.old' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'users.new' => ['bail', 'nullable', 'array', 'exists:users,id'],
            'short_name' => ['bail', 'required', 'string', 'max:10'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'short_name' => 'Nama pendek unit',
            'users.old' => 'Pengguna',
            'users.new' => 'Pengguna',
            'name' => 'Nama unit',
        ];
    }
}
