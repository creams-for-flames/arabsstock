<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VectorMimeType implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pass = FALSE;
        if(is_array($value)){
            $mimeType = mime_content_type($value[0]->path());
            $extention = $value[0]->getClientOriginalExtension();
            $pass = ($mimeType === "application/postscript" && $extention === 'eps')?TRUE:FALSE;
        }

        if(is_object($value)){
            $mimeType = mime_content_type($value->path());
            $extention = $value->getClientOriginalExtension();
            $pass = ($mimeType === "application/postscript" && $extention === 'eps')?TRUE:FALSE;
        }
        return $pass;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.mimes',['values'=>'eps']);
        
    }
}
