<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        $requestUrl = \Request::all();



        if (isset($requestUrl['fxn']) && $requestUrl['fxn'] == "invite") {
            return [
                'adminEmail' => 'required|emails',// comma separated email validation rule is written in service provider file
                'UserEmail' => 'emails' // comma separated email validation rule is written in service provider file
            ];
        }  if (isset($requestUrl['fxn']) && $requestUrl['fxn'] == "addUserInProject") {
            return [
                'email' => 'bail|required|emails', // comma separated email validation rule is written in service provider file
                'role' => 'required',
            ];
        } else {

            return [

                'name' => 'required|string|max:255',
                'adminEmail' => 'required|emails', // comma separated email validation rule is written in service provider file
                'UserEmail' => 'emails' // comma separated email validation rule is written in service provider file
            ];
        }


    }
    public function messages() {
        return [
            'name.alpha_spaces' => 'Project name is required attribute only accept alpha characters, space, and hyphen.',
            'name.alpha_spaces' => 'Please enter only alphabet',
            'name.required' => 'The project name field is required.'

        ];
    }

}
