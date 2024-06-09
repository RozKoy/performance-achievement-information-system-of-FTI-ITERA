<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Closure;

class Unique implements ValidationRule
{
    protected $model, $route;

    public function __construct(Model $model, string $route)
    {
        $this->model = $model;
        $this->route = $route;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $instance = request()->route($this->route);

        if ($instance[$attribute] !== $value) {
            $temp = $this->model::withTrashed()
                ->whereKeyNot($instance->id)
                ->where($attribute, $value)
                ->first();

            if ($temp !== null) {
                $fail(':attribute sudah digunakan');
            }
        }
    }
}
