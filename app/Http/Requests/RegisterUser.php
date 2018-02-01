<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUser extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'bail|required|unique:users',
            'password' => 'bail|required|min:6|max:12',
            'password_confirmation' => 'bail|required|min:6|max:12|same:password',
            'countries'   => 'required',
            'timezone' => 'required',
        ];
    }
}
