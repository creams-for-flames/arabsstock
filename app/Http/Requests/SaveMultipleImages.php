<?php

namespace App\Http\Requests;

use App\Rules\CheckHashImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveMultipleImages extends FormRequest
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
           /* 
        47.6837 MB = 50000000 byte = 48828.11 kb
        2 MB = 2097152 byte = 2048 kb
        allowedFileExtensions: ['jpg', 'mp4', 'png', 'jpeg'],
        */
        
        return [
            'files.*' => ['required','file','mimes:jpg,png,jpeg', 'min_mb:0.5','max_mb:50',new CheckHashImage()],

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
