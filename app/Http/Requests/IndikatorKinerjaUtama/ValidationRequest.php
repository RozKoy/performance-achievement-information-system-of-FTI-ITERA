<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class ValidationRequest extends FormRequest
{
    use RequestErrorMessage;

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
            'data' => ['bail', 'nullable', 'array'],

            'data.*.note' => ['bail', 'nullable', 'string', 'max:255'],
            'data.*.status' => ['bail', 'nullable', 'string'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'data' => 'Daftar validasi',

            'data.*.status' => 'Status',
            'data.*.note' => 'Catatan',
        ];
    }
}
