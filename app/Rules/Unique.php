<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Closure;

class Unique implements ValidationRule
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = request()->route('id');

        if ($id !== null && gettype($value) === 'string') {
            $instance = $this->model::findOrFail($id);

            if ($instance[$attribute] !== $value) {
                $temp = $this->model::whereKeyNot($id)
                    ->where($attribute, $value)
                    ->first();

                if ($temp !== null) {
                    $fail(':attribute sudah digunakan');
                }
            }
        } else {
            abort(404);
        }
    }
}
