<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidBooleanString implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value === "true" || $value === "false" || $value === true || $value === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.boolean');
    }
}
