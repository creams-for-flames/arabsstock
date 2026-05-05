<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
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
            'photographers.*.name' => 'required|string',
            'photographers.*.id_number' => 'required|string',
            'photographers.*.email' => 'required|email',
            'actors.*.name' => 'required|string',
            'actors.*.id_number' => 'required|string',
            'actors.*.email' => 'required|email',
            'country' => 'required|string',
            'city' => 'required|string',
            'location_id' => 'required|string',
            'location_admin' => 'required|string',
            'location_email' => 'required|email',
            'location_mobile' => 'required|string',
            'location_license' => 'required|string',
            'location' => 'required|string',
            'invoices.*.name' => 'required|string',
            'invoices.*.cost' => 'required|numeric',
            'notes' => 'nullable|string',
        ];
    }
}
