<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckVideoFrameRate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $frame_rates = [23.98, 24, 25, 29.97, 30, 47.95, 47.96, 48, 50, 59.94, 60];
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
        $ffprobe = env('FFPROBE_PATH');
        
        try {
            $cmd = "$ffprobe -v error -select_streams v -of default=noprint_wrappers=1:nokey=1 -show_entries stream=r_frame_rate  $value";
            ob_start();
            passthru($cmd);
            $details = ob_get_contents();
            ob_end_clean();
            $frame_rate = \str_replace("\n",'',$details)??NULL;
            $patten = '/[\*\/\+-]/';
            preg_match($patten,$frame_rate, $operator);
            $arr = preg_split($patten,$frame_rate);
            switch($operator[0]){
            case '-':
                $frame_rate = $arr[0] - $arr[1];
            case '+':
                $frame_rate = $arr[0] + $arr[1];
            case '*':
                $frame_rate = $arr[0] * $arr[1];
            case '/':
                $frame_rate = $arr[0] / $arr[1];
            }
            $frame_rate = round($frame_rate,2);
            $pass = isset($frame_rate) && is_numeric($frame_rate) && in_array($frame_rate ,$this->frame_rates)?TRUE:FALSE;
            return $pass;
        } catch (\Throwable $th) {
            \Log::error($th->getMessage().' line - '. $th->getLine());
            return $pass;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.frame_rates',['frame_rates'=> implode(",",$this->frame_rates)]);
    }
}
