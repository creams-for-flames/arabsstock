<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckVideoHaveAudio implements Rule
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
        $seconds = 60;
        $Stream ='Stream #';
        $search = "/Audio.*?([0-9]{1,})/";
        $ffprobe = env('FFPROBE_PATH');
        try {
            $cmd = "$ffprobe -i  $value  2>&1 | grep $Stream | wc -l";
            // $cmd = "$ffprobe -hide_banner -loglevel error -select_streams v:0 -show_streams -of json $value";
            ob_start();
            passthru($cmd);
            $details = ob_get_contents();// \json_decode(trim(ob_get_contents()));
            ob_end_clean();
          // $data = $details;//->streams[0];
          // $tags_language = $data->tags->language;
          // $pass = $tags_language === "eng";
            $check_contain_any_audio = preg_match($search, $details, $matches, PREG_OFFSET_CAPTURE, 13);
             $pass = isset($check_contain_any_audio) && $check_contain_any_audio === 1?FALSE:TRUE;
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
        return __('validation.footage_should_not_contain_any_audio');
    }
}
/*

tags
/test video with audio
 +"tags": {#413
    +"creation_time": "2020-09-12T22:33:20.000000Z"
    +"language": "und"
    +"handler_name": "L-SMASH Video Handler"
    +"encoder": "AVC Coding"
  }
/ive video
  +"tags": {#413
    +"creation_time": "2019-02-09T12:23:53.000000Z"
    +"language": "eng"
    +"handler_name": "\x1FMainconcept Video Media Handler"
    +"encoder": "AVC Coding"
  }

*/
