<?php

namespace App\Http\Requests;

use App\Rules\VideoDuration;
use App\Rules\CheckHashVideo;
use App\Rules\VideoDimension;
use App\Rules\CheckVideoFrameRate;
use App\Rules\CheckVideoHaveAudio;
use App\Rules\CheckVideoCodecsAllowed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadViedio extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'files.*' => ['required',
            'file',
            'mimes:mp4,mpeg,flv,mkv',
             'min_mb:4',
            'max_mb:3814',
            new VideoDimension(640,480),
            new CheckVideoHaveAudio(),
            new VideoDuration(),
            new CheckVideoFrameRate(),
            new CheckVideoCodecsAllowed(),
            new CheckHashVideo(),
        ]

        ]; 
    }

    protected function failedValidation(Validator $validator) {
        $errors_keys = $validator->errors()->keys();
        $request_errors = $validator->errors()->all();
        $errors = [];
        for ($i = 0; $i < sizeof($errors_keys); $i++) {
            $errors[\str_replace('.0','',$errors_keys[$i])] = \str_replace('.0','',$request_errors[$i]) ;
        }

       $exception = [
            'success' => false,
            'message' => 'Validation Errors',
            'code' => 2,
            'errors' => $errors,
        ];
        throw new HttpResponseException(response()->json($exception, 422));
    }


}
