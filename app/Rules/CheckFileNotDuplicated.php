<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Stock;

class CheckFileNotDuplicated implements Rule
{
    protected $table;
    protected $type;

    // publish
    public function __construct($table, $type)
    {
        $this->table = $table;
        $this->type = $type;
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
        $pass = TRUE;
        if ($this->type === 'publish') {
            $ids = \explode(',', $value);
            $data = \DB::table($this->table)->whereIn('contributor_image_id', $ids)->whereNull('deleted_at')->select('id')->first();
            $pass = $data ? FALSE : TRUE;
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
        return __('validation.file_contributor_exist');

    }
}
