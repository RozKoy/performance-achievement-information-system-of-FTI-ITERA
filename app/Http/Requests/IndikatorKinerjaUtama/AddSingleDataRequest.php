<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class AddSingleDataRequest extends FormRequest
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
            'link' => ['bail', 'nullable', 'string', 'max:5000'],
            'value' => ['bail', 'nullable', 'numeric'],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            'link' => 'Link Bukti',
            'value' => 'Nilai',
        ];
    }
}
