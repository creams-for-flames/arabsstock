<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VideoDimension implements Rule
{
    protected $minWidth;
    protected $minHeight;

    public function __construct($minWidth, $minHeight)
    {
        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
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
        $pass = true;
        try {
                $ffprobe = env('FFPROBE_PATH');
                $cmd = "$ffprobe  -v error -select_streams v:0 -show_entries stream=width,height -of json $value";
                ob_start();
                passthru($cmd);
                $details = \json_decode(trim(ob_get_contents()));
                ob_end_clean();
                $data = $details->streams[0];
                $resolution['width'] = $data->width; //"1920×1080"
                $resolution['height'] = $data->height; //"1920×1080"
        
                if ($this->minWidth > $resolution['width']
                    || $this->minHeight > $resolution['height']){
                    $pass = false;
                }

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
       return  __('validation.minimal_resolution_allowed',['dimensions'=>"  SD – 640×480 703×576 720×480 720×486 720×576"]);
    }
}