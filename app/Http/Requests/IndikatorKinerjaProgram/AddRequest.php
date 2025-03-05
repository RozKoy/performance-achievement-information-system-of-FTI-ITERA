<?php

namespace App\Http\Requests\IndikatorKinerjaProgram;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IndikatorKinerjaProgram;
use App\Traits\RequestErrorMessage;

class AddRequest extends FormRequest
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
            'mode' => ['bail', 'nullable', 'string', 'in:' . implode(',', IndikatorKinerjaProgram::getModeValues())],
            'number' => ['bail', 'required', 'numeric', 'integer', 'min:1', 'max_digits:10'],
            'definition' => ['bail', 'required', 'string', 'max:65000'],
            'name' => ['bail', 'required', 'string', 'max:65000'],
            'file' => ['bail', 'nullable', 'string', 'max:500'],
            'columns.*' => ['bail', 'string', 'max:500'],
            'type' => ['bail', 'required', 'in:iku,ikt'],
            'columns' => ['bail', 'nullable', 'array'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'definition' => 'Definisi operasional',
            'name' => 'Program strategis',
            'type' => 'Tipe pendukung',
            'columns.*' => 'Kolom',
            'columns' => 'Kolom',
            'number' => 'Nomor',
            'file' => 'File',
            'mode' => 'Mode',
        ];
    }
}
