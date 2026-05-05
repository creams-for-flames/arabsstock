<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckHashVector implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pass = FALSE;
        try {
            $data_hash = hash_file('sha256', $value->path());
            \Log::channel('info')->info('Rule CheckHashVector : ' . $data_hash);
            $hash = \DB::table('vectors')->where('hash', 'like', '%' . $data_hash . '%')->select('id', 'hash')->first();
            if (!$hash)
                $hash = \DB::table('contributor_vectors')->where('hash', 'like', '%' . $data_hash . '%')->select('id', 'hash')->first();

            $pass = $hash ? FALSE : TRUE;
        } catch (\Throwable $th) {
            $auth_id = 'userId: ' . auth()->id();
            \Log::error($auth_id . ' Rule CheckHashVector : ' . $th->getMessage() . ' line: ' . $th->getLine());
            return $pass;
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
        return __('validation.file_danger_copyright');

    }
}
