<?php

namespace App\Http\Requests\IndikatorKinerjaUtama;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestErrorMessage;

class SetDeadlineRequest extends FormRequest
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
            $this->route('period')?->id . '-deadline' => ['bail', 'required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
        ];
    }

    /**
     * Aliases name
     * @return array
     */
    public function attributes(): array
    {
        return [
            $this->route('period')?->id . '-deadline' => 'Batas waktu',
        ];
    }
}
