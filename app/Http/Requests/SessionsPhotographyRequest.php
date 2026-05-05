<?php

namespace App\Http\Requests;

use App\Rules\UniqueIdNumber;
use App\Rules\UniqueIdOrEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SessionsPhotographyRequest extends FormRequest
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
        $rules =  [
            'folder' => 'required|string',
            'session_date' => 'required|date',
            'photographers' => ['required','array'],
            'photographers.*.name' => 'required|string',
            'photographers.*.id_number' => ['required',new UniqueIdOrEmail('photographers')],
            'photographers.*.email' => 'required|email',
            'photographers.*.contract' => 'required|string',
            'actors' => 'required|array',
            'actors.*.name' => 'required|string',
            'actors.*.id_number' => ['required',new UniqueIdOrEmail('actors')],
            'actors.*.email' => 'required|email',
            'actors.*.contract' => 'required|string',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'location_id' => 'nullable',
            'location_name' => 'required|string',
            'location_admin' => 'required|string',
            'location_email' => 'required|email',
            'location_mobile' => 'required|string',
            'license_code' => 'required|string',
            'location' => 'required|string',
            'contract' => 'required|string',
            'invoices' => 'required|array',
            'invoices.*.name' => 'required|string',
            'invoices.*.cost' => 'required|numeric',
            'notes' => 'required|string',
        ];
        if ($this->route()->getName() === 'admin.sessions.update') {
            # code...
            $rules['invoices.*.file'] = 'sometimes|file'; // You may need to customize this based on your file upload handling.
            
        }else{
            $rules['invoices.*.file'] = 'required|file'; // You may need to customize this based on your file upload handling.

        }

        return $rules;

    }
    
}
