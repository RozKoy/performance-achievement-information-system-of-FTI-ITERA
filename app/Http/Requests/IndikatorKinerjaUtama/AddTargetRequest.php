<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;

class AddTargetRequest extends FormRequest
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
            'target.*' => ['bail', 'nullable', 'numeric', 'integer', 'min:0', 'max_digits:11'],
        ];
    }

    public function attributes(): array
    {
        return [
            'target.*' => 'Target',
        ];
    }

    public function messages(): array
    {
        return [
            'max_digits' => ':attribute tidak boleh melebihi :max digit',
            'integer' => ':attribute harus berupa bilangan bulat',
            'min' => ':attribute tidak boleh kurang dari :min',
            'numeric' => ':attribute harus berupa bilangan',
        ];
    }
}
