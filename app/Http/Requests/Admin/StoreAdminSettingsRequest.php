<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminSettingsRequest extends FormRequest
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
            'result_request' => 'max:2147483647|nullable|numeric',
            'limit_upload_user' => 'max:2147483647|nullable|numeric',
            'message_length' => 'max:2147483647|nullable|numeric',
            'comment_length' => 'max:2147483647|nullable|numeric',
            'file_size_allowed' => 'max:2147483647|nullable|numeric',
            'tags_limit' => 'max:2147483647|nullable|numeric',
            'description_length' => 'max:2147483647|nullable|numeric',
        ];
    }
}
