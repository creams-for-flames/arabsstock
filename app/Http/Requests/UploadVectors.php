<?php

namespace App\Http\Requests;

use App\Rules\VectorMimeType;
use App\Rules\CheckHashVector;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class UploadVectors extends FormRequest
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

        // dd($this);
        /* 
        47.6837 MB = 50000000 byte = 48828.11 kb
        2 MB = 2097152 byte = 2048 kb
        4 MB = 2048 * 2 = 4096; 
        allowedFileExtensions: ['EPS'],
        */
        return [
            'files.*' => ['required','file','mimes:eps', 'min_mb:0.29','max_mb:50',
             new CheckHashVector()
        ],

        ]; 
    }
    public function attributes()
    {
        return [
            'files.*' => __('validation.file'),
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
    public function messages()
    {
        return [
            'files.*.mimes' => __('validation.vector_not_support_eps') . ",".__('validation.mimes',['values'=>'eps']),

        ];
    }
}
