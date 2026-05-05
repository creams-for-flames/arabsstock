<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ExistsUser implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $type = "exists";

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
        $user = User::where('email', $value);

        $exist = $user->count();

        if ($exist)
            $user = $user->where('status', 'active');
        if (!$exist)
            return false;

        $user = $user->first();
        if ($user->bounced_emails()->count()) {
            $this->type = "bounced_email";
            Log::error("Forgot Password route:  " .request()->getRequestUri(). " bounced  email: {$value}" );
            return false;
        }
        if ($user) {
            return true;
        } elseif ($exist) {
            $this->type = "user_suspended";
        }
        return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("validation.{$this->type}");
    }
}
