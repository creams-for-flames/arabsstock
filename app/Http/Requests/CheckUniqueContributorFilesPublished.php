<?php

namespace App\Http\Requests;

use App\Rules\CheckFileNotDuplicated;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckUniqueContributorFilesPublished extends FormRequest
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
            'ids' => ['required', new CheckFileNotDuplicated('images', $this->type)],


        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors_keys = $validator->errors()->keys();
        $request_errors = $validator->errors()->all();
        $errors = [];
        for ($i = 0; $i < sizeof($errors_keys); $i++) {
            $errors['message'] = \str_replace('.0', '', $request_errors[$i]);
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
