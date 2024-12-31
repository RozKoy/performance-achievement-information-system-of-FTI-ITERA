<?php

namespace App\Http\Requests\RencanaStrategis;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class ImportRequest extends FormRequest
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
            'file' => ['bail', 'required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'file' => 'File',
        ];
    }
}
