<?php

namespace App\Http\Requests\RencanaStrategis;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
            'file' => ['bail', 'required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'File',
        ];
    }

    public function messages(): array
    {
        return [
            'mimes' => ':attribute harus berbentuk .csv/.xls/xlsx',
            'file' => ':attribute harus berupa file',
            'required' => ':attribute wajib diisi',
        ];
    }
}
