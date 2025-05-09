<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class AddEvaluationRequest extends FormRequest
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
            'evaluation' => ['bail', 'nullable', 'string', 'max:255'],
            'follow_up' => ['bail', 'nullable', 'string', 'max:255'],
            'target' => ['bail', 'nullable', 'numeric'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'follow_up' => 'Tindak lanjut',
            'evaluation' => 'Kendala',
            'target' => 'Target',
        ];
    }
}
