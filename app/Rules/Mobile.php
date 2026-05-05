<?php

namespace App\Rules;

use App\Helper;
use Illuminate\Contracts\Validation\Rule;

class Mobile implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Helper::isValidTelephoneNumber($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.mobile');
    }
}
