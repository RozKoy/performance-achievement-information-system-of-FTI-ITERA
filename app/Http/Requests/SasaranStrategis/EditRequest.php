<?php

namespace App\Http\Requests\SasaranStrategis;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
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
            'number' => ['bail', 'required', 'numeric', 'integer', 'min:1', 'max_digits:10'],
            'name' => ['bail', 'required', 'string', 'max:65000'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'Sasaran strategis',
            'number' => 'Nomor',
        ];
    }

    /**
     * Error message
     * @return array
     */
    public function messages(): array
    {
        return [
            'max_digits' => ':attribute tidak boleh melebihi :max digit',
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'integer' => ':attribute harus berupa bilangan bulat',
            'min' => ':attribute tidak boleh kurang dari :min',
            'numeric' => ':attribute harus berupa bilangan',
            'string' => ':attribute harus berupa teks',
            'required' => ':attribute wajib diisi',
        ];
    }
}
