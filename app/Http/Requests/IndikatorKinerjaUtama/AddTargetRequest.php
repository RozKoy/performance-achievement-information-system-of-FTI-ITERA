<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class AddTargetRequest extends FormRequest
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
            'target.*' => ['bail', 'nullable', 'numeric', 'min:0'],
            'target' => ['bail', 'array'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'target.*' => 'Target',
            'target' => 'Target',
        ];
    }
}
