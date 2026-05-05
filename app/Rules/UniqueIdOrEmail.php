<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueIdOrEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $table;
    public function __construct($table)
    {
        $this->table = $table;
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
        $table = $this->table;
        $email = request()->input(str_replace('id_number','email',$attribute));
        
        $data =  DB::table($table);


        $together = $data->where('id_number', $value)->where('email', $email)->count();
        if ($together) {
            return TRUE;
        }else{
            $one = $data
            ->orWhere('id_number', $value)->orWhere('email', $email)->count();
            if ($one) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The ID number and email must be unique together';
    }
}
