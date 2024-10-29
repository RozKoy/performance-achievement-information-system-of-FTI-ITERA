<?php

namespace App\Http\Requests\RencanaStrategis;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'link-*' => ['bail', 'nullable', 'string', 'max:5000'],
            'realization-*' => ['bail', 'nullable', 'max:255'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'realization-*' => 'Realisasi',
            'link-*' => 'Link Bukti',
        ];
    }

    /**
     * Error message
     * @return array
     */
    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi :max karakter',
        ];
    }
}
