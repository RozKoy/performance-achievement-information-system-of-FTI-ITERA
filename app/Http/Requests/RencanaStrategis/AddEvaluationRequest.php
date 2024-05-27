<?php

namespace App\Http\Requests\RencanaStrategis;

use Illuminate\Foundation\Http\FormRequest;

class AddEvaluationRequest extends FormRequest
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
            'realization' => ['bail', 'nullable', 'max:255'],
            'evaluation' => ['bail', 'nullable', 'max:255'],
            'follow_up' => ['bail', 'nullable', 'max:255'],
            'period' => ['bail', 'required', 'in:1,2,3'],
            'status' => ['bail', 'nullable', 'boolean'],
            'target' => ['bail', 'nullable', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'follow_up' => 'Tindak lanjut',
            'realization' => 'Realisasi',
            'evaluation' => 'Evaluasi',
            'period' => 'Periode',
            'status' => 'Status',
            'target' => 'Target',
        ];
    }

    public function messages(): array
    {
        return [
            'max' => ':attribute tidak boleh melebihi :max karakter',
            'boolean' => ':attribute harus bernilai boolean',
            'required' => ':attribute wajib diisi',
            'in' => ':attribute tidak sesuai',
        ];
    }
}
