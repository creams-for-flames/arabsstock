<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPlansRequest extends FormRequest
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

            'starts_at' => 'nullable|date_format:'.config('app.date_format'),
            'ends_at' => 'nullable|date_format:'.config('app.date_format'),
            //'days_remaining' => 'max:2147483647|nullable|numeric',
            'download_remaining' => 'max:2147483647|nullable|numeric',
        ];
    }
}
