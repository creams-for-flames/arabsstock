<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VideoDuration implements Rule
{
  private $min_seconds = 5;
  private $max_seconds = 60;
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
        try {
            $ffprobe = env('FFPROBE_PATH');
            $cmd = "$ffprobe  -v error -select_streams v:0 -show_entries stream=duration -of json $value"; // get stream duration
            // $cmd = "$ffprobe -hide_banner -loglevel error -select_streams v:0 -show_streams -of json $value"; //get all stream
            ob_start();
            passthru($cmd);
            $details = \json_decode(trim(ob_get_contents()));
            ob_end_clean();
            $data = $details->streams[0];
            $duration = isset($data->duration)?round($data->duration ,2):NULL;

            $pass = isset($duration) && ($this->min_seconds <= $duration)
                    && ($duration <= $this->max_seconds)?TRUE:FALSE;

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
        return __('validation.duration_video',['min'=>$this->min_seconds,'max'=>$this->max_seconds]);
    }
}
/*
live**********
{#414
  +"index": 0
  +"codec_name": "h264"
  +"codec_long_name": "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10"
  +"profile": "High"
  +"codec_type": "video"
  +"codec_time_base": "1/50"
  +"codec_tag_string": "avc1"
  +"codec_tag": "0x31637661"
  +"width": 1920
  +"height": 1080
  +"coded_width": 1920
  +"coded_height": 1088
  +"has_b_frames": 1
  +"pix_fmt": "yuv420p"
  +"level": 42
  +"color_range": "tv"
  +"color_space": "bt709"
  +"color_transfer": "bt709"
  +"color_primaries": "bt709"
  +"chroma_location": "left"
  +"refs": 1
  +"is_avc": "true"
  +"nal_length_size": "4"
  +"r_frame_rate": "25/1"
  +"avg_frame_rate": "25/1"
  +"time_base": "1/25000"
  +"start_pts": 0
  +"start_time": "0.000000"
  +"duration_ts": 400000
  +"duration": "16.000000"
  +"bit_rate": "20074375"
  +"bits_per_raw_sample": "8"
  +"nb_frames": "400"
  +"disposition": {#412
    +"default": 1
    +"dub": 0
    +"original": 0
    +"comment": 0
    +"lyrics": 0
    +"karaoke": 0
    +"forced": 0
    +"hearing_impaired": 0
    +"visual_impaired": 0
    +"clean_effects": 0
    +"attached_pic": 0
    +"timed_thumbnails": 0
  }
  +" ffprobe tags language": {#413
    +"creation_time": "2020-10-03T13:54:01.000000Z"
    +"language": "eng"
    +"handler_name": "\x1FMainconcept Video Media Handler"
    +"encoder": "AVC Coding"
  }
}
live***************

{#414
  +"index": 0
  +"codec_name": "h264"
  +"codec_long_name": "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10"
  +"profile": "High"
  +"codec_type": "video"
  +"codec_time_base": "1/50"
  +"codec_tag_string": "avc1"
  +"codec_tag": "0x31637661"
  +"width": 1920
  +"height": 1080
  +"coded_width": 1920
  +"coded_height": 1088
  +"has_b_frames": 2
  +"pix_fmt": "yuv420p"
  +"level": 40
  +"color_range": "tv"
  +"color_space": "bt709"
  +"color_transfer": "bt709"
  +"color_primaries": "bt709"
  +"chroma_location": "left"
  +"refs": 1
  +"is_avc": "true"
  +"nal_length_size": "4"
  +"r_frame_rate": "25/1"
  +"avg_frame_rate": "25/1"
  +"time_base": "1/25"
  +"start_pts": 0
  +"start_time": "0.000000"
  +"duration_ts": 247
  +"duration": "9.880000"
  +"bit_rate": "4990034"
  +"bits_per_raw_sample": "8"
  +"nb_frames": "247"
  +"disposition": {#412
    +"default": 1
    +"dub": 0
    +"original": 0
    +"comment": 0
    +"lyrics": 0
    +"karaoke": 0
    +"forced": 0
    +"hearing_impaired": 0
    +"visual_impaired": 0
    +"clean_effects": 0
    +"attached_pic": 0
    +"timed_thumbnails": 0
  }
  +"tags": {#413
    +"creation_time": "2020-09-12T22:33:20.000000Z"
    +"language": "und"
    +"handler_name": "L-SMASH Video Handler"
    +"encoder": "AVC Coding"
  }
}


*/
